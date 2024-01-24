CREATE TABLE `si_equipe` (
  `id` int NOT NULL,
  `nome` varchar(256) DEFAULT NULL,
  `tipo` varchar(128) DEFAULT NULL,
  `data_criacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `si_funcao` (
  `id` int NOT NULL,
  `nome` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `si_secao` (
  `id` int NOT NULL,
  `nome` varchar(256) DEFAULT NULL,
  `data_criacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `si_pessoa` (
  `id` int NOT NULL,
  `nome` varchar(256) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `data_criacao` datetime DEFAULT NULL,
  `funcao_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `si_bem_patrimonio` (
  `id` int NOT NULL,
  `nome` varchar(256) NOT NULL,
  `descricao` text,
  `patrimonio` varchar(128) NOT NULL,
  `status` int NOT NULL,
  `data_criado` datetime DEFAULT NULL,
  `responsavel_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `si_responsavel` (
  `id` int NOT NULL,
  `nome` varchar(256) DEFAULT NULL,
  `tipo` varchar(128) DEFAULT NULL,
  `entidade_id` int DEFAULT NULL,
  `data_criacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;