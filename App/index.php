<?php
namespace App;

require "../vendor/autoload.php";
use App\Model\Cliente;
use App\Repository\ClienteRepository;

header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

switch ($_SERVER['REQUEST_METHOD']) {
    // OPTIONS
    case 'OPTIONS':

        $allowed_methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
        http_response_code(200);
        echo json_encode($allowed_methods);
        break;

    // POST
    case 'POST':
        
        $requiredFields = ['nome', 'email', 'cidade', 'estado'];

        $data = json_decode(file_get_contents("php://input")); 
        

        if (!isValid($data, $requiredFields)) {
            http_response_code(400); 
            echo json_encode(["error" => "Dados de entrada inválidos."]);
            break;
        }

        $cliente = new Cliente();

        $cliente->setNome($data->nome);
        $cliente->setEmail($data->email);
        $cliente->setCidade($data->cidade);
        $cliente->setEstado($data->estado);

        $repository = new ClienteRepository();
        $success = $repository->insert($cliente);
        
        if ($success) {
            http_response_code(201); 
            echo json_encode(["message" => "Dados inseridos com sucesso."]);
        } else {
            http_response_code(500); 
            echo json_encode(["message" => "Falha ao inserir dados."]);
        }
        break;

    // GET
    case 'GET':
        try{

            $cliente = new Cliente();
            $repository = new ClienteRepository();

            if (isset($_GET['id'])) {
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);


                if ($id === false) {
                    http_response_code(400); 
                    echo json_encode(['error' => 'O valor do ID fornecido não é um inteiro válido.']);
                    exit;
                } else {
                    $cliente->setClienteId($id);
                    $result = $repository->getById($cliente);
                }
            } else {
                $result = $repository->getAll();
            }


            if ($result) {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404); 
                echo json_encode(["message" => "Nenhum dado encontrado."]);
            }
        } catch(Exception $error){
            http_response_code(500); 
            echo json_encode(["message" => "Erro: " . $error->getMessage()]);
        }

        break;

    // PUT
    case 'PUT':

        $data = json_decode(file_get_contents("php://input"));

        $requiredFields = ['nome', 'email', 'cidade', 'estado'];
        

        if (!isValid($data, $requiredFields)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados de entrada inválidos."]);
            break;
        }

        $cliente = new Cliente();
        $repository = new ClienteRepository();

        $cliente->setNome($data->nome);
        $cliente->setEmail($data->email);
        $cliente->setCidade($data->cidade);
        $cliente->setEstado($data->estado);

        if(isset($data->cliente_id)){
            $cliente->setClienteId($data->cliente_id);

            if($repository->getById($cliente)){

                $success = $repository->update($cliente);
                

                if ($success) {
                    http_response_code(200);
                    echo json_encode(["message" => "Dados atualizados com sucesso."]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Falha ao atualizar dados."]);
                }
            } else { 
                http_response_code(404); 
                echo json_encode(["message" => "Falha ao atualizar, nenhum dado encontrado."]);
            }
        } else { 

            $success = $repository->insert($cliente);
            
            if ($success) {
                http_response_code(200); 
                echo json_encode(["message" => "Dados inseridos com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Falha ao inserir dados."]);
            }
        }
        
        break;

    // DELETE
    case 'DELETE':

        $data = json_decode(file_get_contents("php://input")); 

        $requiredFields  = ['id'];

        if (!isValid($data, $requiredFields)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados de entrada inválidos."]);
            break;
        }


        $id = $data->id;

      
        $cliente = new Cliente();   
        $cliente->setClienteId($id);


        $repository = new ClienteRepository();

        $result = $repository->getById($cliente);

        if(!$result){
            http_response_code(404); 
            echo json_encode(["message" => "Nenhum dado encontrado."]);
        }
        
        $success = $repository->delete($cliente);

        if ($success) {
            http_response_code(200); 
            echo json_encode(["message" => "Dados apagados com sucesso."]);
        } else {
            http_response_code(500); 
            echo json_encode(["message" => "Falha ao apagar dados."]);
        }

        break;

    default:
        http_response_code(405); 
        echo json_encode(["error" => "Método não permitido."]);
        break;
}

function isValid($data, $requiredFields) {
    foreach ($requiredFields as $field) {
        if (!isset($data->$field)) {
            return false;
        }
    }
    return true;
}