<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Token-Acces");
header("Content-Type: application/json");

include_once "./src/database/model/sistema.php";
$sistema = new Sistema;

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathName = basename($url);
$jsonData = json_decode(file_get_contents("php://input"), true);


$inputPost = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$inputGet = filter_input_array(INPUT_GET, FILTER_DEFAULT);
$response = [];
$permission = getallheaders();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  switch ($pathName) {

    case 'users':
      $response['admin'] = $sistema->CadastrarAdmin(
        $permission['Token-Acces'],
        $inputPost['name'],
        $inputPost['username'],
        $inputPost['permission']
      );
      break;

    case "setpassword":
      $response['check'] = $sistema->adicionarSenhaAdmin(
        $jsonData['username'],
        $jsonData['password']
      );
      break;

    case "authlogin":
      $response['login'] = $sistema->LoginAdmin(
        $inputPost['username'],
        $inputPost['password']
      );
    default:

    case "viaturas":
      $response['viaturas'] = $sistema->cadastrarVEiculo(
        $permission['Token-Acces'],
        $inputPost['marca'],
        $inputPost['modelo'],
        $inputPost['tipo'],
        $inputPost['matricula']
      );
      break;

    case "condutores":
      if (!empty($jsonData)) {
        $response['condutores'] = $sistema->cadastrarCondutor(
          $permission['Token-Acces'],
          $jsonData['nome'],
          $jsonData['naturalidade'],
          $jsonData['genero'],
          $jsonData['pai_nome_completo'],
          $jsonData['mae_nome_completo'],
          $jsonData['estado_civil'],
          $jsonData['data_nasc'],
          $jsonData['bilhete'],
          $jsonData['telefone']
        );
      } else {
        $response['result'] = $sistema->cadastrarCondutor(
          $permission['Token-Acces'],
          $inputPost['nome'],
          $inputPost['naturalidade'],
          $inputPost['genero'],
          $inputPost['pai_nome_completo'],
          $inputPost['mae_nome_completo'],
          $inputPost['estado_civil'],
          $inputPost['data_nasc'],
          $inputPost['bilhete'],
          $inputPost['telefone']
        );
      }
      break;

    case "infracoes":
      $response['result'] = $sistema->cadastrarInfracoes(
        $permission['Token-Acces'],
        $inputPost['id'],
        $inputPost['infracao_tipo'],
        $inputPost['descricao'],
        $inputPost['data_infracao'],
        $inputPost['localizacao'],
        $inputPost['valor_multa'],
        "Pendente"
      );
      break;
  }
  echo json_encode($response);
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
  switch ($pathName) {

    case 'users':
      if (isset($inputGet['search']) && !empty($inputGet['search'])) {
        $response['search'] = $sistema->pesquisarUsuario($permission['Token-Acces'], $inputGet['search']);
      }
      if (isset($inputGet['id']) && !empty($inputGet['id'])) {
        $response['getId'] = $sistema->listDataAdminById($permission['Token-Acces'], $inputGet['id']);
      }
      $response['admin'] = $sistema->listDataAdmin($permission['Token-Acces']);
      break;

    case 'auth':
      $response['auth'] = $sistema->GetAuth($permission['Token-Acces']);
      break;

    case 'estatisticas':
      $response['estatisticas'] = $sistema->Estatisticas($permission['Token-Acces']);
      break;

    case 'login':
      $response['username'] = $sistema->listUsernamesAdmin();
      break;

    case "checkusername":
      if (!empty($inputGet['username'])) {
        $response['check'] = $sistema->VerificarSenhaAdmin($inputGet['username']);
      }
      break;

    case "dashboard":
      $response['dashboard'] = $sistema->Dashboard($permission['Token-Acces']);
      break;

    case "viaturas":
      if (isset($inputGet['id'])) {
        $response['result'] = $sistema->listarViaturasById(
          $permission['Token-Acces'],
          $inputGet['id']
        );
      } else {
        $response['viaturas'] = $sistema->listarViaturas($permission['Token-Acces']);
      }
      break;

    case "infracoes":
      if (isset($inputGet['id'])) {
        $response['result'] = $sistema->ListarInfracoesBy(
          $permission['Token-Acces'],
          $inputGet['id']
        );
      }
      if (isset($inputGet['search'])) {
        $response['search'] = $sistema->PesquisarInfracoes($permission['Token-Acces'], $inputGet['search']);
      }
      $response['infracoes'] = $sistema->ListarInfracoes($permission['Token-Acces']);
      break;

    case "searchviaturas":
      if (isset($inputGet['data'])) {
        $response['result'] = $sistema->pesquisarViatuas(
          $permission['Token-Acces'],
          $inputGet['data']
        );
      }
      break;

    case "searchcondutores":
      if (isset($inputGet['search'])) {
        $response['search'] = $sistema->pesquisarCondutor(
          $permission['Token-Acces'],
          $inputGet['search']
        );
      }
      break;

    case "condutores":
      if (isset($inputGet['bi'])) {
        $response['condutores'] = $sistema->listarCondutoresBI($permission['Token-Acces'], $inputGet['bi']);
      }
      if (isset($inputGet['id'])) {
        $response['result'] = $sistema->listarCondutoresByTd(
          $permission['Token-Acces'],
          $inputGet['id']
        );
      }
      $response['data'] = $sistema->listarCondutores($permission['Token-Acces']);
      break;

    default:
      break;
  }
  echo json_encode($response);
}

if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
  switch ($pathName) {

    case "viaturas":
      $response['delete'] = $sistema->eliminarViaturas(
        $permission['Token-Acces'],
        $jsonData['id']
      );
      break;

    case "users":
      $response['delete'] = $sistema->DeleteAdmin($permission['Token-Acces'], $jsonData['id'], $jsonData['password']);
      break;

    case "condutores":
      $response['result'] = $sistema->eliminarCondutor(
        $permission['Token-Acces'],
        $jsonData['id']
      );
      break;

    case "infracoes":
      $response['result'] = $sistema->EliminarInfracoes($permission['Token-Acces'], $jsonData['id']);
      break;
    default:

      break;
  }
  echo json_encode($response);
}

if ($_SERVER['REQUEST_METHOD'] == "PUT") {
  switch ($pathName) {

    case "reset":
      $response['reset'] = $sistema->ResetPassword($permission['Token-Acces'], $jsonData['password']);
      break;

    case "logout":
      $response['logout'] = $sistema->LogOut($permission['Token-Acces']);
      break;

    case "viaturas":
      $response['update'] = $sistema->atualizarVeiculo(
        $permission['Token-Acces'],
        $jsonData['marca'],
        $jsonData['modelo'],
        $jsonData['tipo'],
        $jsonData['matricula'],
        $jsonData['id']
      );
      break;

    case "blockusers":
      $response['users'] = $sistema->BloqueateUser($permission['Token-Acces'], $jsonData['id'], $jsonData['password']);
      break;

    case "debloqusers":
      $response['users'] = $sistema->DeBloqueateUser($permission['Token-Acces'], $jsonData['id'], $jsonData['password']);
      break;

    case "users":
      $response['admin'] = $sistema->AtualizarAdmin(
        $permission['Token-Acces'],
        $jsonData['name'],
        $jsonData['username'],
        $jsonData['permission'],
        $jsonData['id']
      );
      break;

    case "infracoes":
      if (isset($jsonData['status']) && !empty($jsonData['status'])) {
        $response['statusUpdate'] = $sistema->atualizarStatusInfracoes(
          $permission['Token-Acces'],
          $jsonData['status'],
          $jsonData['id']
        );
      } else {
        $response['update'] = $sistema->atualizarInfracoes(
          $permission['Token-Acces'],
          $jsonData['id'],
          $jsonData['infracao_tipo'],
          $jsonData['descricao'],
          $jsonData['data_infracao'],
          $jsonData['localizacao'],
          $jsonData['valor_multa']
        );
      }
      break;
    default:

      break;
  }
  echo json_encode($response);
}
