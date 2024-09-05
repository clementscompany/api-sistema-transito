<?php
date_default_timezone_set('Africa/Luanda');
class Sistema
{
  private $dbHost = "localhost";
  private $dbUsername = "root";
  private $dbPassword = "";
  private $dbName = "sistema_transito";
  private $conn;
  private $acces_key = "488721william@";
  public $response = [];
  public function __construct()
  {
    $this->response;
  }
  public function connect()
  {
    try {
      $this->conn = mysqli_connect($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
      if ($this->conn) {
        return $this->conn;
      }
    } catch (Exception $th) {
      return $th->getMessage();
    }
  }

  public function AtualizarAdmin($auth, $nome, $username, $permission, $id)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("UPDATE admin SET nome = ?, username = ?, permission = ? WHERE admin.id = ?");
          $sql2->bind_param("sssi", $nome, $username, $permission, $id);
          if ($sql2->execute()) {
            $this->response['sucess'] = "Atualizado com sucesso!";
          } else {
            $this->response['error'] = "Erro desconhecido!";
          }
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function CadastrarAdmin($auth, $nome, $username, $permission)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key)) {
          $sql = $this->conn->prepare("SELECT * FROM admin WHERE admin.username = ?");
          $sql->bind_param("s", $username);
          if ($sql->execute()) {
            $result = $sql->get_result();
            if ($result->num_rows > 0) {
              $this->response['error'] = "Estes dados já foram cadastrados!";
            } else {
              $sql2 = $this->conn->prepare("INSERT INTO admin (nome, username, permission, status) VALUES (?, ?, ?, 0)");
              $sql2->bind_param("sss", $nome, $username, $permission);
              if ($sql2->execute()) {
                $this->response['sucess'] = "Cadastrado com sucesso!";
              } else {
                $this->response['error'] = "Erro desconhecido!";
              }
            }
          }
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function VerificarSenhaAdmin($username)
  {
    try {
      if ($this->connect()) {
        $sql1 = $this->conn->prepare("SELECT senha FROM admin WHERE admin.username = ?");
        $sql1->bind_param("s", $username);
        $sql1->execute();
        $result = $sql1->get_result();
        if ($result->num_rows > 0) {
          $data = $result->fetch_assoc();
          if ($data['senha'] !== null) {
            $this->response['sucess'] = true;
          } else {
            $this->response['empty'] = true;
          }
        } else {
          $this->response['empty'] = true;
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }


  public function DeBloqueateUser($auth, $id, $password)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) !== false) {
          $username = $this->decodeJWTadmin($auth, $this->acces_key);
          $sql = mysqli_query($this->conn, "SELECT * FROM admin WHERE admin.username = '{$username}'");
          $data = mysqli_fetch_assoc($sql);
          if (password_verify($password, $data['senha'])) {
            if ($data['id'] !== $id) {
              $sql2 = $this->conn->prepare("UPDATE admin SET acess = 1  WHERE admin.id = ?");
              $sql2->bind_param("i", $id);
              $sql2->execute();
              $this->response['sucess'] = "Desbloqueado com sucesso!";
            } else {
              $this->response['error'] = "Ação negada!";
            }
          } else {
            $this->response['error'] = "Senha incorreta!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Dados nao encontrados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }
  public function BloqueateUser($auth, $id, $password)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) !== false) {
          $username = $this->decodeJWTadmin($auth, $this->acces_key);
          $sql = mysqli_query($this->conn, "SELECT * FROM admin WHERE admin.username = '{$username}'");
          $data = mysqli_fetch_assoc($sql);
          if (password_verify($password, $data['senha'])) {
            if ($data['id'] !== $id) {
              $sql2 = $this->conn->prepare("UPDATE admin SET acess = 0  WHERE admin.id = ?");
              $sql2->bind_param("i", $id);
              $sql2->execute();
              $this->response['sucess'] = "Bloqueado com sucesso!";
            } else {
              $this->response['error'] = "Ação negada!";
            }
          } else {
            $this->response['error'] = "Senha incorreta!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Dados nao encontrados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }
  public function adicionarSenhaAdmin($username, $password)
  {
    try {
      if ($this->connect()) {
        $sql1 = $this->conn->prepare("SELECT senha FROM admin WHERE admin.username = ?");
        $sql1->bind_param("s", $username);
        $sql1->execute();
        $result = $sql1->get_result();
        if ($result->num_rows > 0) {
          $data = $result->fetch_assoc();
          if ($data['senha'] !== null) {
            $this->response['error'] = "Você já possue uma senha!";
          } else {
            $senha = password_hash($password, PASSWORD_DEFAULT);
            $update = $this->conn->prepare("UPDATE admin SET senha = ? WHERE admin.username = ?");
            $update->bind_param("ss", $senha, $username);
            if ($update->execute()) {
              $this->response['sucess'] = "Senha adicionada com sucesso!";
            } else {
              $this->response['error'] = "Erro desconhecido";
            }
          }
        } else {
          $this->response['error'] = "Dados nao encontrados!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function LoginAdmin($username, $password)
  {
    try {
      if ($this->connect()) {
        $sql = $this->conn->prepare("SELECT * FROM admin WHERE admin.username = ?");
        $sql->bind_param("s", $username);
        if ($sql->execute()) {
          $result = $sql->get_result();
          if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $time = time() * 60 * 24 * 7;
            if (password_verify($password, $data['senha'])) {
              if (intval($data['acces']) === 0) {
                $this->response['error'] = "Seu acesso foi bloqueado!";
              }

              $token = $this->genereteJWT_admin($data, $time, $this->acces_key);
              $sql2 = $this->conn->prepare("UPDATE admin SET status = 1 WHERE admin.id = ?");
              $sql2->bind_param("i", $data['id']);
              $sql2->execute();
              $this->response['sucess'] = "Dados confirmados com sucesso!";
              $this->response['token'] = $token;
            } else {
              $this->response['error'] = "Usuário ou senha incorretos!";
            }
          } else {
            $this->response['error'] = "Usuário ou senha incorretos!";
          }
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function atualizarDadosAdmin($auth, $nome, $username, $id)
  {
    try {
      if ($this->connect() && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare("UPDATE admin SET nome = ?, username = ? WHERE admin.id = ?");
        $sql->bind_param("ssi", $nome, $username, $id);
        if ($sql->execute()) {
          $this->response['sucess'] = "Dados atualizados com sucesso!";
        } else {
          $this->response['error'] = "Erro ao atualizar os dados";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }


  public function ResetPassword($auth, $password)
  {
    try {
      if ($this->connect()) {
        $username = $this->decodeJWTadmin($auth, $this->acces_key);

        if ($username !== false) {
          $stmt = $this->conn->prepare("SELECT * FROM admin WHERE admin.username = ?");
          $stmt->bind_param("s", $username);
          $stmt->execute();
          $result = $stmt->get_result();
          $data = $result->fetch_assoc();

          if (password_verify($password, $data['senha'])) {
            $stmt2 = $this->conn->prepare("UPDATE admin SET senha = NULL WHERE admin.id = ?");
            $stmt2->bind_param("i", $data['id']);
            $stmt2->execute();
            $this->response['sucess'] = "Senha restaurada com sucesso, termine a sessão e inicie novamente!";
          } else {
            $this->response['error'] = "Senha incorreta!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Dados não encontrados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }


  public function DeleteAdmin($auth, $id, $password)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) !== false) {
          $username = $this->decodeJWTadmin($auth, $this->acces_key);
          $sql = mysqli_query($this->conn, "SELECT * FROM admin WHERE admin.username = '{$username}'");
          $data = mysqli_fetch_assoc($sql);
          if (password_verify($password, $data['senha'])) {
            if ($data['id'] !== $id) {
              $sql2 = $this->conn->prepare("DELETE FROM admin  WHERE admin.id = ?");
              $sql2->bind_param("i", $id);
              $sql2->execute();
              $this->response['sucess'] = "Eliminado com sucesso!";
            } else {
              $this->response['error'] = "Ação negada!";
            }
          } else {
            $this->response['error'] = "Senha incorreta!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Dados nao encontrados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function listDataAdmin($auth)
  {
    try {
      if ($this->connect() && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare("SELECT * FROM admin ORDER BY id DESC");
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
          while ($data = $result->fetch_assoc()) {
            $this->response['sucess'][] = $data;
          }
        } else {
          $this->response['error'] = "Sem registros!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function listDataAdminById($auth, $id)
  {
    try {
      if ($this->connect() && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare("SELECT * FROM admin WHERE admin.id = ? LIMIT 1");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
          $data = $result->fetch_assoc();
          $this->response['sucess'] = $data;
        } else {
          $this->response['error'] = "Sem registros!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function searchAdmin($auth, $data)
  {
    try {
      $like = "%$data%";
      if ($this->connect() && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare("SELECT nome, username, id FROM admin WHERE nome LIKE ? OR username LIKE ? OR id LIKE ?");
        $sql->bind_param("sss", $like, $like, $like);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
          while ($data = $result->fetch_assoc()) {
            $this->response['sucess'][] = $data;
          }
        } else {
          $this->response['error'] = "Nenhum registro encontrado!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function GetAuth($auth)
  {
    try {
      if ($this->connect() && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $username = $this->decodeJWTadmin($auth, $this->acces_key);
        $sql = $this->conn->prepare("SELECT * FROM admin WHERE admin.username = ? LIMIT 1");
        $sql->bind_param("i", $username);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
          $data = $result->fetch_assoc();
          $this->response['sucess'] = $data;
        } else {
          $this->response['error'] = "Sem registros!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function LogOut($auth)
  {
    try {
      if ($this->connect() && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $username = $this->decodeJWTadmin($auth, $this->acces_key);
        $sql = $this->conn->prepare("UPDATE admin SET status = 0 WHERE admin.username = ? ");
        $sql->bind_param("s", $username);
        if ($sql->execute()) {
          $this->response['sucess'] = "Sucesso!";
        } else {
          $this->response['error'] = "Erro desconhecido!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }
  public function listUsernamesAdmin()
  {
    try {
      if ($this->connect()) {
        $sql = $this->conn->prepare("SELECT username FROM admin ORDER BY id DESC");
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
          while ($data = $result->fetch_assoc()) {
            $this->response['sucess'][] = $data;
          }
        } else {
          $this->response['error'] = "Sem registros!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  // ((((((((((((((((((((((((((((((((((((((((((((FUNCIONÁRIOS)))))))))))))))))))))))))))))))))))))))))))) 



  //cadastrar usuarios 
  public function cadastrarUser($auth, $nome_completo, $username)
  {
    try {
      if ($this->connect()  && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare("SELECT * FROM usuarios WHERE usuarios.username = ? ");
        $sql->bind_param("s", $username);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
          $this->response['error'] = "Este usuario já foi cadastrado!";
        } else {
          $sql2 = $this->conn->prepare("INSERT INTO usuarios (nome_completo, username) VALUES (?,?)");
          $sql2->bind_param("ss", $nome_completo, $username);
          if ($sql2->execute()) {
            $this->response['sucess'] = "Cadastrado com sucesso!";
          } else {
            $this->response['error'] = "Erro ao cadastrar o usuario!";
          }
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }


  //listar usuarios 
  public function listarUsusrios($auth)
  {
    try {
      if ($this->connect()  && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare("SELECT * FROM usuarios ORDER BY id DESC ");
        $sql->execute();
        $result = $sql->get_result();
        if ($result->num_rows > 0) {
          while ($data = $result->fetch_assoc()) {
            $this->response['sucess'][] = $data;
          }
        } else {
          $this->response['error'] = "Sem registros!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  //atualizar usuario
  public function atualizadUsusrio($auth, $nomeCompleto, $username, $id)
  {
    try {
      if ($this->connect()  && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare(
          "UPDATE usuarios SET nome_completo = ?, username = ? 
                WHERE usuarios.id = ? "
        );
        $sql->bind_param("ssi", $nomeCompleto, $username, $id);
        if ($sql->execute()) {
          $this->response['sucess'] = "Dados atualizados com sucesso!";
        } else {
          $this->response['error'] = "Erro desconhecido!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  //eliminar usuario
  public function eliminarUsuario($auth, $id)
  {
    try {
      if ($this->connect()  && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $sql = $this->conn->prepare("DELETE FROM usuarios WHERE usuarios.id = ? ");
        $sql->bind_param("i", $id);
        if ($sql->execute()) {
          $this->response['sucess'] = "Dados eliminados com sucesso!";
        } else {
          $this->response['error'] = "Erro desconhecido!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  //pesquisar usuario 
  public function pesquisarUsuario($auth, $data)
  {
    try {
      if ($this->connect() && $this->decodeJWTadmin($auth, $this->acces_key)) {
        $like = "%$data%";
        $sql = $this->conn->prepare("SELECT nome, id, username FROM admin WHERE admin.nome LIKE ? OR admin.username LIKE ? OR admin.id LIKE ?");
        $sql->bind_param("sss", $like, $like, $like);
        if ($sql->execute()) {
          $result = $sql->get_result();
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $this->response['sucess'][] = $row;
            }
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Erro desconhecido!";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  // (((((((((((((((((((((((((((((((((((CONDUTORES)))))))))))))))))))))))))))))))))))

  public function cadastrarCondutor(
    $auth,
    $nome,
    $naturalidade,
    $genero,
    $pai_nome_completo,
    $mae_nome_completo,
    $estado_civil,
    $data_nasc,
    $bilhete,
    $telefone
  ) {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $consulta = $this->conn->prepare("SELECT * FROM condutores WHERE condutores.bilhete = ?");
          $consulta->bind_param("s", $bilhete);
          $consulta->execute();
          $result = $consulta->get_result();
          if ($result->num_rows > 0) {
            $this->response['error'] = "Estes dados já foram cadastrados!";
          } else {
            $sql = $this->conn->prepare("INSERT INTO condutores 
                (
                nome, naturalidade, genero, 
                pai_nome_completo, mae_nome_completo, 
                estado_civil, data_nasc, bilhete, telefone
                ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $sql->bind_param(
              "sssssssss",
              $nome,
              $naturalidade,
              $genero,
              $pai_nome_completo,
              $mae_nome_completo,
              $estado_civil,
              $data_nasc,
              $bilhete,
              $telefone
            );
            if ($sql->execute()) {
              $this->response['sucess'] = "Cadastrado com sucesso!";
            } else {
              $this->response['error'] = "Erro desconhecido!";
            }
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }


  public function editarCondutores($auth, $id, $nome_completo, $bilhete, $genero, $telefone)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("UPDATE condutores SET nome_completo = ?, bilhete = ? genero = ?,telefone = ? WHERE condutores.id = ? ");
          $sql2->bind_param("ssssi", $nome_completo, $bilhete, $genero, $telefone, $id);
          if ($sql2->execute()) {
            $this->response['sucess'] = "Atualizado com sucesso!";
          } else {
            $this->response['error'] = "Erro ao atualizar os dados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function listarCondutoresBI($auth, $bi)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("SELECT * FROM  condutores WHERE condutores.bilhete = ? LIMIT 1");
          $sql2->bind_param("s", $bi);
          $sql2->execute();
          $result = $sql2->get_result();

          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['sucess'][] = $data;
            }
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function listarCondutores($auth)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("SELECT * FROM  condutores  ORDER BY id DESC");
          $sql2->execute();
          $result = $sql2->get_result();

          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['sucess'][] = $data;
            }
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function listarCondutoresByTd($auth, $id)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("SELECT * FROM  condutores  WHERE id = ? LIMIT 1");
          $sql2->bind_param("i", $id);
          $sql2->execute();
          $result = $sql2->get_result();

          if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $this->response['sucess'][] = $data;
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function eliminarCondutor($auth, $id)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("DELETE FROM  condutores  WHERE condutores.id = ? ");
          $sql2->bind_param("i", $id);
          if ($sql2->execute()) {
            $this->response['sucess'] = "Eliminado com sucesso!";
          } else {
            $this->response['error'] = "Erro ao eliminar os dados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function pesquisarCondutor($auth, $data)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $like = "%$data%";
          $sql2 = $this->conn->prepare(
            "SELECT * FROM condutores  
          WHERE condutores.id LIKE ? 
          OR condutores.nome LIKE ? OR 
          condutores.telefone LIKE ? OR 
          condutores.bilhete LIKE ?"
          );
          $sql2->bind_param("ssss", $like, $like, $like, $like);
          if ($sql2->execute()) {
            $result = $sql2->get_result();

            if ($result->num_rows > 0) {
              while ($data = $result->fetch_assoc()) {
                $this->response['sucess'][] = $data;
              }
            } else {
              $this->response['error'] = "Nenhum registro encontrado!";
            }
          } else {
            $this->response['error'] = "Erro ao pesquisar os dados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  // (((((((((((((((((((((((((((((((((((((INFRACOES)))))))))))))))))))))))))))))))))))))
  public function cadastrarInfracoes(
    $auth,
    $condutor_id,
    $infracao_tipo,
    $descricao,
    $data_infracao,
    $localizacao,
    $valor_multa,
    $status_pagamento
  ) {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $verify = $this->conn->prepare(
            "SELECT * FROM infracoes_transito 
          WHERE infracoes_transito.infracao_tipo = ? AND infracoes_transito.descricao = ?"
          );
          $verify->bind_param("ss", $infracao_tipo, $descricao);
          $verify->execute();
          $result_verify = $verify->get_result();
          if ($result_verify->num_rows > 0) {
            $this->response['error'] = "Estes dados já foram cadastrados!";
          } else {
            $post = $this->conn->prepare("INSERT INTO infracoes_transito
          (condutor_id,infracao_tipo,descricao,data_infracao,localizacao,valor_multa,status_pagamento) 
          values (?, ? ,? ,? ,?, ?, ?)
          ");
            $post->bind_param(
              "issssss",
              $condutor_id,
              $infracao_tipo,
              $descricao,
              $data_infracao,
              $localizacao,
              $valor_multa,
              $status_pagamento
            );
            if ($post->execute()) {
              $this->response['sucess'] = "Cadastrado com sucesso!";
            } else {
              $this->response['error'] = "Erro desconhecido!";
            }
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function atualizarStatusInfracoes($auth, $status, $id)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $verify = $this->conn->prepare(
            "SELECT * FROM infracoes_transito 
          WHERE infracoes_transito.id = ?"
          );
          $verify->bind_param("i", $id);
          $verify->execute();
          $result_verify = $verify->get_result();
          if ($result_verify->num_rows > 0) {
            $update = $this->conn->prepare("UPDATE infracoes_transito SET status_pagamento = ?
            WHERE infracoes_transito.id = ? ");
            $update->bind_param("si", $status, $id);
            if ($update->execute()) {
              $this->response['sucess'] = "Atualizado com sucesso!";
            } else {
              $this->response['error'] = "Erro desconhecido ao atualizar!";
            }
          } else {
            $this->response['error'] = "Dados não encontrados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function atualizarInfracoes(
    $auth,
    $id,
    $infracao_tipo,
    $descricao,
    $data_infracao,
    $localizacao,
    $valor_multa,
  ) {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $verify = $this->conn->prepare(
            "SELECT * FROM infracoes_transito 
          WHERE infracoes_transito.id = ?"
          );
          $verify->bind_param("i", $id);
          $verify->execute();
          $result_verify = $verify->get_result();
          if ($result_verify->num_rows > 0) {
            $update = $this->conn->prepare("UPDATE infracoes_transito SET
            infracao_tipo = ?, descricao = ?, data_infracao = ?, localizacao = ?, valor_multa = ?
            WHERE id = ?
          ");
            $update->bind_param(
              "sssssi",
              $infracao_tipo,
              $descricao,
              $data_infracao,
              $localizacao,
              $valor_multa,
              $id
            );
            if ($update->execute()) {
              $this->response['sucess'] = "Atualizado com sucesso!";
            } else {
              $this->response['error'] = "Erro desconhecido ao atualizar!";
            }
          } else {
            $this->response['error'] = "Dados não encontrados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }



  //pesquisar infracoes
  public function PesquisarInfracoes($auth, $data)
  {

    try {
      if ($this->connect()) {
        if (
          $this->decodeJWTadmin($auth, $this->acces_key)
          || $this->decodeJWTclient($auth, $this->acces_key)
        ) {
          $like = $data;
          $data = "%$data%";

          $search = $this->conn->prepare("SELECT 
        inf.id,
        inf.infracao_tipo,
        inf.localizacao,
        inf.descricao,
        inf.condutor_id,
        inf.status_pagamento,
        condutores.nome
        FROM infracoes_transito AS inf
        INNER JOIN condutores ON condutores.id = inf.condutor_id 
        WHERE  inf.infracao_tipo LIKE ? OR inf.localizacao LIKE ? OR inf.descricao LIKE ?");
          $search->bind_param("sss", $data, $data, $data);
          $search->execute();
          $result = $search->get_result();
          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['sucess'][] = $data;
            }
            $this->response['detail'] =  $like;
          } else {
            $this->response['detail'] =  $like;
            $this->response['error'] = "Registros não encontrados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function EliminarInfracoes($auth, $id)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key)) {
          $delete = $this->conn->prepare("DELETE FROM infracoes_transito WHERE infracoes_transito.id = ?");
          $delete->bind_param("i", $id);
          if ($delete->execute()) {
            $this->response['sucess'] = "Eliminado com sucesso!";
          } else {
            $this->response['error'] = "Erro ao eliminar o regisro!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  // listar infracoes
  public function   ListarInfracoes($auth)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("SELECT 
         inf.id,
         inf.condutor_id,
         inf.descricao,
         inf.data_infracao,
         inf.localizacao,
         inf.valor_multa,
         inf.status_pagamento,
         inf.criado_em,
         inf.infracao_tipo,
         cond.nome 
         FROM  infracoes_transito AS inf 
         INNER  JOIN condutores AS cond ON cond.id = inf.condutor_id
         ORDER BY inf.id DESC");

          $totalInfracoes = mysqli_query($this->conn, "SELECT COUNT(*) as contagem FROM infracoes_transito");
          $total = mysqli_fetch_assoc($totalInfracoes);
          $this->response['total'] = $total['contagem'];
          $sql2->execute();
          $result = $sql2->get_result();
          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['sucess'][] = $data;
            }
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }
  public function Estatisticas($auth)
  {
    try {
      if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
        if ($this->connect()) {
          $getCount = mysqli_query($this->conn, "SELECT COUNT(*) AS totalInfracoes FROM infracoes_transito");
          $maiores = mysqli_query($this->conn, "SELECT COUNT(*) AS maiores FROM infracoes_transito 
        WHERE valor_multa > '50000.00'");
          $pendents = mysqli_query($this->conn, "SELECT COUNT(*) AS pendentes FROM infracoes_transito 
        WHERE status_pagamento = 'Pendente'");
          $pagas = mysqli_query($this->conn, "SELECT COUNT(*) AS pagas FROM infracoes_transito 
        WHERE status_pagamento = 'Pago'");
          $totalMultas = mysqli_query($this->conn, "SELECT SUM(valor_multa) AS totalMultas FROM infracoes_transito");
          $mediaMultas = mysqli_query($this->conn, "SELECT AVG(valor_multa) AS mediaMultas FROM infracoes_transito");
          $hoje = date('Y-m-d');
          $getToday = mysqli_query($this->conn, "SELECT COUNT(*) as dadosHoje FROM infracoes_transito 
        WHERE DATE(infracoes_transito.data_infracao) = '{$hoje}'");


          $count = mysqli_fetch_assoc($getCount);
          $maioresCount = mysqli_fetch_assoc($maiores);
          $pendentsCount = mysqli_fetch_assoc($pendents);
          $pagasCount = mysqli_fetch_assoc($pagas);
          $totalMultasSum = mysqli_fetch_assoc($totalMultas);
          $mediaMultasAvg = mysqli_fetch_assoc($mediaMultas);
          $result_today = mysqli_fetch_assoc($getToday);

          $media = intval($mediaMultasAvg['mediaMultas']);
          $total = intval($count['totalInfracoes']);
          $total_multas = intval($totalMultasSum['totalMultas']);

          $media = number_format($media, 2, ",", ".");
          $total_multas = number_format($total_multas, 2, ",", ".");


          $this->response['totalInfracoes'] = $total;
          $this->response['maiores'] = $maioresCount['maiores'];
          $this->response['pendentes'] = $pendentsCount['pendentes'];
          $this->response['pagas'] = $pagasCount['pagas'];
          $this->response['totalMultas'] = $total_multas;
          $this->response['mediaMultas'] = $media;
          $this->response['regisrosAtuais'] = $result_today['dadosHoje'];
        } else {
          $this->response['error'] = "Erro ao conectar ao banco de dados.";
        }
      } else {
        $this->response['error'] = "Acesso negado!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }

    return $this->response;
  }

  public function ListarInfracoesBy($auth, $id)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $sql2 = $this->conn->prepare(
            "SELECT 
         inf.id,
         inf.condutor_id,
         inf.descricao,
         inf.data_infracao,
         inf.localizacao,
         inf.valor_multa,
         inf.status_pagamento,
         inf.criado_em,
         inf.infracao_tipo,
         cond.nome  
         FROM  infracoes_transito AS inf
         INNER JOIN condutores AS cond
         WHERE inf.id = ? "
          );
          $sql2->bind_param("i", $id);
          $sql2->execute();
          $result = $sql2->get_result();
          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['sucess'][] = $data;
            }
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  // ((((((((((((((((((((((((((((((((((((((VEICULO))))))))))))))))))))))))))))))))))))))

  public function cadastrarVEiculo($auth, $marca, $modelo, $tipo, $matricula)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $sql = $this->conn->prepare("SELECT * FROM viaturas WHERE viaturas.matricula = ?");
          $sql->bind_param("s", $matricula);
          $sql->execute();
          $result = $sql->get_result();
          if ($result->num_rows > 0) {
            $this->response['error'] = "Estes dados já foram cadastrados!";
          } else {
            $sql2 = $this->conn->prepare("INSERT INTO viaturas (marca, modelo, tipo, matricula) VALUES (?, ?, ?, ?)");
            $sql2->bind_param("ssss", $marca, $modelo, $tipo, $matricula);
            if ($sql2->execute()) {
              $this->response['sucess'] = "Cadastrado com sucesso!";
            } else {
              $this->response['error'] = "Erro ao cadastrar!";
            }
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function listarViaturas($auth)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {

          $sql2 = $this->conn->prepare("SELECT * FROM  viaturas  ORDER BY id DESC");
          $sql2->execute();
          $result = $sql2->get_result();

          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['sucess'][] = $data;
            }
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function Dashboard($auth)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {


          $sql2 = $this->conn->prepare("SELECT * FROM viaturas ORDER BY id DESC LIMIT 20");
          $sql2->execute();
          $result = $sql2->get_result();


          $countUser = $this->conn->prepare("SELECT COUNT(*) AS total FROM condutores");
          $countUser->execute();
          $resultCont = $countUser->get_result();
          $dataCount = $resultCont->fetch_assoc();

          $countViaturas = $this->conn->prepare("SELECT COUNT(*) AS totalviaturas FROM viaturas");
          $countViaturas->execute();
          $result_viaturas = $countViaturas->get_result();
          $data_viaturas = $result_viaturas->fetch_assoc();
          $this->response['totalViaturas'] = $data_viaturas['totalviaturas'];


          $this->response['totalcond'] = $dataCount['total'];


          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['success'][] = $data;
            }
          } else {
            $this->response['error'] = "Sem registros recentes!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }


  public function listarViaturasById($auth, $id)
  {
    try {
      if ($this->connect()) {
        if (
          $this->decodeJWTadmin($auth, $this->acces_key) ||
          $this->decodeJWTclient($auth, $this->acces_key)
        ) {

          $sql2 = $this->conn->prepare("SELECT * FROM  viaturas  WHERE viaturas.id = ? LIMIT 1");
          $sql2->bind_param("i", $id);
          $sql2->execute();
          $result = $sql2->get_result();

          if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $this->response['sucess'][] = $data;
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      } else {
        $this->response['error'] = "Erro de conexão com o banco de dados!";
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }


  public function pesquisarViatuas($auth, $data)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $like = "%$data%";
          $sql = $this->conn->prepare("SELECT * FROM viaturas WHERE marca LIKE ? OR modelo LIKE ? OR tipo LIKE ? OR matricula LIKE ?");
          $sql->bind_param("ssss", $like, $like, $like, $like);
          $sql->execute();

          $result = $sql->get_result();
          if ($result->num_rows > 0) {
            while ($data = $result->fetch_assoc()) {
              $this->response['sucess'][] = $data;
            }
          } else {
            $this->response['error'] = "Sem registros!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function atualizarVeiculo($auth, $marca, $modelo, $tipo, $matricula, $id)
  {
    try {
      if ($this->connect()) {
        if ($this->decodeJWTadmin($auth, $this->acces_key) || $this->decodeJWTclient($auth, $this->acces_key)) {
          $sql = $this->conn->prepare("UPDATE viaturas SET marca = ?, modelo = ?, tipo = ?, matricula = ? WHERE viaturas.id = ?");
          $sql->bind_param("ssssi", $marca, $modelo, $tipo, $matricula, $id);
          $sql->execute();
          if ($sql->execute()) {
            $this->response['sucess'] = "Dados atualizados com sucesso!";
          } else {
            $this->response['error'] = "Erro ao atualizar os dados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function eliminarViaturas($auth, $id)
  {
    try {
      if ($this->connect()) {
        if (
          $this->decodeJWTadmin($auth, $this->acces_key) ||
          $this->decodeJWTclient($auth, $this->acces_key)
        ) {
          $sql = $this->conn->prepare("DELETE FROM viaturas WHERE id = ?");
          $sql->bind_param("i", $id);
          $sql->execute();
          if ($sql->execute()) {
            $this->response['sucess'] = "Dados eliminados com sucesso!";
          } else {
            $this->response['error'] = "Erro ao eliminar os dados!";
          }
        } else {
          $this->response['error'] = "Acesso negado!";
        }
      }
    } catch (Exception $th) {
      $this->response['error'] = $th->getMessage();
    }
    return $this->response;
  }

  public function genereteJWT_admin($data, $data_expire, $acces_key)
  {
    $header = [
      "alg" => "HS256",
      "typ" => "JWT",
    ];
    $header = json_encode($header);
    $header = base64_encode($header);

    $payload = [
      "username" => $data['username'],
      "permission" => $data['permission'],
      "exp" => $data_expire,
      "acess" => $data['acess']
    ];
    $payload = json_encode($payload);
    $payload = base64_encode($payload);

    $assinature = hash_hmac("sha256", "$header.$payload", $acces_key, true);
    $assinature = base64_encode($assinature);
    $token = "$header.$payload.$assinature";

    return $token;
  }

  public function genereteJWT_client($data, $data_expire, $acces_key, $id)
  {
    $header = [
      'alg' => "HS256",
      'typ' => "JWT",
    ];
    $header = json_encode($header);
    $header = base64_encode($header);

    $payload = [
      'username' => $data['username'],
      'exp' => $data_expire,
      'permission' => $data['permission'],
      'userid' => $id,
    ];

    $payload = json_encode($payload);
    $payload = base64_encode($payload);

    $assinature = hash_hmac("sha256", "$header.$payload", $acces_key, true);
    $assinature = base64_encode($assinature);

    $token = "$header.$payload.$assinature";
    return $token;
  }

  public function decodeJWTclient($jwt, $acces_key)
  {
    $arrJWT = explode(".", $jwt);
    $header = $arrJWT[0];
    $payload = $arrJWT[1];
    $assinature = $arrJWT[2];

    $header = base64_decode($header);
    $header = json_decode($header);

    $payload = base64_decode($payload);
    $payload = json_decode($payload);
    if ($header->alg == "HS256" && $header->typ == "JWT") {
      if ($payload->exp > time() && $payload->permission == "client") {

        $newHeader = json_encode($header);
        $newHeader = base64_encode($newHeader);

        $newPayLoad = json_encode($payload);
        $newPayLoad = base64_encode($newPayLoad);

        $newAssinature = hash_hmac("sha256", "$newHeader.$newPayLoad", $acces_key, true);
        $newAssinature = base64_encode($newAssinature);

        if ($newAssinature === $assinature) {

          $sendData = base64_decode($newPayLoad);
          $sendData = json_decode($sendData);

          $data = [
            'id' => $sendData->userid,
            'username' => $sendData->username
          ];

          return $data;
        } else {
          return false;
        }
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public static function decodeJWTadmin($jwt, $acces_key)
  {
    $arrayJWT = explode(".", $jwt);
    $heaer = $arrayJWT[0];
    $payload = $arrayJWT[1];
    $assignature = $arrayJWT[2];

    $newToken = hash_hmac("sha256", "$heaer.$payload", $acces_key, true);
    $newToken = base64_encode($newToken);

    if (!empty($jwt)) {
      if ($newToken === $assignature) {
        $payload = base64_decode($payload);
        $payload = json_decode($payload);
        if ($payload->permission === "root" && $payload->exp > time()) {
          $username = $payload->username;
          return $username;
        }
        return false;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}
