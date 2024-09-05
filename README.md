# API-Sistema transito
- instalacao Servidor ```apache ``` banco de dados ``mysql``
- configuracao inicial:
- navuegue ate ao diretorio/
- ```sh
  src/database/model/sistema.php
  private $dbHost = "seu_banco";
  private $dbUsername = "seu_usuario";
  private $dbPassword = "sua_senha";
  private $dbName = "sistema_transito";
  private $conn;
  private $acces_key = "crie_uma_chave_de_acesso";

  // importe o banco de dados
  sistema_transito.sql
  ```
  
