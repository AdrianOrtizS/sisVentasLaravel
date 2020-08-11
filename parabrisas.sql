



CREATE TABLE `persona` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `condicion` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `identificador` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;





CREATE TABLE `proveedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT '',
  `direccion` varchar(100) DEFAULT '',
  `foto` varchar(255) DEFAULT '',
  `condicion` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `identificador` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `celular` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;





CREATE TABLE `tipoproducto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `condicion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;



CREATE TABLE `producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `condicion` int(1) DEFAULT NULL,
  `idtipo` int(1) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `tipoproducto` (`idtipo`),
  CONSTRAINT `tipoproducto` FOREIGN KEY (`idtipo`) REFERENCES `tipoproducto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;




CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `identificador` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `condicion` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;


INSERT INTO `users` VALUES ('15', 'Adrian', 'Ortiz', 'adrian-2222@hotmail.com', '1718348053', '05ba4fe260d2dfeadcd35cae072b44eca637e96d83ef99803e221c8207551914', null, 'Administrador', '<p><em><u>Administrador </u></em>del sitio</p>', '1584303255cr7.jpg', '1', '2020-02-24 22:59:14', '2020-03-15 20:14:19');
INSERT INTO `users` VALUES ('21', 'jessie', 'luna', 'jess.1919@hotmail.com', '1721300042', '906fdfaa32340bfc412a988ec11fd22d4a159f4151c020cb0292aa33434664a3', null, 'Vendedor', '<p>vendedor</p>', '1584394786d.jpg', '1', '2020-03-16 21:39:18', '2020-03-16 21:40:50');




CREATE TABLE `ingreso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idproveedor` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `numcomprobante` decimal(10,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `subtotal` decimal(10,2) DEFAULT NULL,
  `condicion` varchar(1) DEFAULT NULL,
  `iduser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `iduser` (`iduser`),
  KEY `idproveedor` (`idproveedor`) USING BTREE,
  CONSTRAINT `idproveedor` FOREIGN KEY (`idproveedor`) REFERENCES `proveedor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `iduser` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;


CREATE TABLE `detalleingreso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idingreso` int(11) DEFAULT NULL,
  `idproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idingreso` (`idingreso`),
  KEY `idproducto` (`idproducto`),
  CONSTRAINT `idingreso` FOREIGN KEY (`idingreso`) REFERENCES `ingreso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idproducto` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8;







CREATE TABLE `ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idpersona` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `numcomprobante` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `subtotal` decimal(10,2) DEFAULT NULL,
  `condicion` varchar(1) DEFAULT NULL,
  `iduser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idpersona` (`idpersona`),
  KEY `iduser` (`iduser`),
  CONSTRAINT `idpersonav` FOREIGN KEY (`idpersona`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `iduserv` FOREIGN KEY (`iduser`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;




CREATE TABLE `detalleventa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idventa` int(11) DEFAULT NULL,
  `idproducto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idventa` (`idventa`),
  KEY `idproducto` (`idproducto`),
  CONSTRAINT `idproductov` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idventav` FOREIGN KEY (`idventa`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;



DELIMITER ;;
CREATE TRIGGER `tr_updStockIngreso` AFTER INSERT ON `detalleingreso` FOR EACH ROW BEGIN
    		UPDATE producto SET stock = stock + NEW.cantidad 
		WHERE producto.id = NEW.idproducto;
END
;;
DELIMITER ;




DELIMITER ;;
CREATE TRIGGER `tr_updStockAnular` AFTER UPDATE ON `ingreso` FOR EACH ROW begin
  update producto p
    join detalleingreso di
      on di.idproducto = p.id
     and di.idingreso = new.id
     set p.stock = p.stock - di.cantidad;
end
;;
DELIMITER ;
