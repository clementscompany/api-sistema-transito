-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 05-Set-2024 às 09:28
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema_transito`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(244) NOT NULL,
  `username` varchar(244) NOT NULL,
  `senha` varchar(110) DEFAULT NULL,
  `permission` varchar(110) DEFAULT NULL,
  `acess` tinyint(1) DEFAULT 1,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `admin`
--

INSERT INTO `admin` (`id`, `nome`, `username`, `senha`, `permission`, `acess`, `status`) VALUES
(2, 'Moises ', 'Clemente', '$2y$10$6SG3REfZaDvuZ.admiPgEO3yKVhAse9RsfnPSmloLAxNPbXUhItb2', 'root', 1, 1),
(4, 'Jaba', 'jabaculer', '$2y$10$hnpMrl9OOo8JNA8TcNZ5AOtMz0IB.aUAiPcAaZFLMdKZOt67PoDfm', 'root', 1, 0),
(5, 'Moises Clemente', 'ajbcompany4881@gmail.com', NULL, 'root', 1, 0),
(7, 'Chaleb Gonzalez', 'Chaleb ', NULL, 'client', 1, 0),
(8, 'Brendo', 'sssssssss', NULL, 'client', 0, 0),
(9, 'Clemente', 'Moises clemente', NULL, 'client', 0, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `condutores`
--

CREATE TABLE `condutores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `naturalidade` varchar(255) DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `pai_nome_completo` varchar(255) DEFAULT NULL,
  `mae_nome_completo` varchar(255) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `data_nasc` varchar(110) DEFAULT NULL,
  `bilhete` varchar(110) NOT NULL,
  `telefone` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `condutores`
--

INSERT INTO `condutores` (`id`, `nome`, `naturalidade`, `genero`, `pai_nome_completo`, `mae_nome_completo`, `estado_civil`, `data_nasc`, `bilhete`, `telefone`) VALUES
(6, 'Moises Capagica Shinguinheca clemente ', 'Luanda', 'Masculino', 'Clemente capagica', 'Teresa Mussenga', 'Solteiro', '2001-05-11', '008586669LS044', 932900352),
(9, 'MOISÉS CAPAGICA SHINGUINHECA CLEMENTE', 'SAURIMO', 'M', 'CLEMENTE CAPAGICA', 'TERESA MUSSENGA SHINGUINHECA', 'SOLTEIRO', '2001-05-11', '008586669LS049', 932900352);

-- --------------------------------------------------------

--
-- Estrutura da tabela `infracoes_transito`
--

CREATE TABLE `infracoes_transito` (
  `id` int(11) NOT NULL,
  `condutor_id` int(11) NOT NULL,
  `infracao_tipo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_infracao` date NOT NULL,
  `localizacao` varchar(255) DEFAULT NULL,
  `valor_multa` decimal(10,2) DEFAULT NULL,
  `status_pagamento` varchar(100) DEFAULT 'Pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `infracoes_transito`
--

INSERT INTO `infracoes_transito` (`id`, `condutor_id`, `infracao_tipo`, `descricao`, `data_infracao`, `localizacao`, `valor_multa`, `status_pagamento`, `criado_em`) VALUES
(14, 6, 'Excesso de velocidade', 'Excesso de velocidade', '2024-08-29', 'Luanda, Angola', 500.00, 'pago', '2024-08-29 16:29:45'),
(15, 6, 'Paragem proibida', 'O motorista estacionou em um local nao autorizado!\n', '2024-09-02', 'Luanda, Via Express', 60000.00, 'pago', '2024-09-02 16:27:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ususrios`
--

CREATE TABLE `ususrios` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(244) NOT NULL,
  `username` varchar(244) NOT NULL,
  `password` varchar(244) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `viaturas`
--

CREATE TABLE `viaturas` (
  `id` int(11) NOT NULL,
  `marca` varchar(244) NOT NULL,
  `modelo` varchar(244) NOT NULL,
  `tipo` varchar(244) NOT NULL,
  `matricula` varchar(22) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `viaturas`
--

INSERT INTO `viaturas` (`id`, `marca`, `modelo`, `tipo`, `matricula`) VALUES
(20, 'Jetur ', 'MLW(22)', 'Ligeiro', '3213123'),
(21, 'V-8', 'Toyota-Pajero', 'Ligeiro', '3445253'),
(22, 'CHEVROLET', 'CAMARO', 'LIGEIRO', '324232840'),
(24, 'Ranger', 'V5', 'Ligeiro', '232323434'),
(25, 'Ranger Rover', 'V4', 'Pesado', '4345345');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `condutores`
--
ALTER TABLE `condutores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `infracoes_transito`
--
ALTER TABLE `infracoes_transito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `condutor_id` (`condutor_id`);

--
-- Índices para tabela `ususrios`
--
ALTER TABLE `ususrios`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `viaturas`
--
ALTER TABLE `viaturas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `condutores`
--
ALTER TABLE `condutores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `infracoes_transito`
--
ALTER TABLE `infracoes_transito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `ususrios`
--
ALTER TABLE `ususrios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `viaturas`
--
ALTER TABLE `viaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `infracoes_transito`
--
ALTER TABLE `infracoes_transito`
  ADD CONSTRAINT `infracoes_transito_ibfk_1` FOREIGN KEY (`condutor_id`) REFERENCES `condutores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
