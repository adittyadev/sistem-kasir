/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 8.0.30 : Database - db_pos_kafe
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_pos_kafe` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `db_pos_kafe`;

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `kode_prefix` varchar(5) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`nama_kategori`,`slug`,`kode_prefix`,`status`) values (1,'Coffee','coffee','COF','aktif'),(2,'Non-Coffee','non-coffee','NCF','aktif'),(3,'Food','food','FOD','aktif'),(4,'Pastry','pastry','PST','aktif');

/*Table structure for table `order_details` */

DROP TABLE IF EXISTS `order_details`;

CREATE TABLE `order_details` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `kuantiti` int NOT NULL,
  `subtotal_baris` decimal(12,2) NOT NULL,
  `catatan_khusus` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_details_order` (`order_id`),
  KEY `idx_details_product` (`product_id`),
  CONSTRAINT `fk_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_details_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `order_details` */

insert  into `order_details`(`id`,`order_id`,`product_id`,`harga_satuan`,`kuantiti`,`subtotal_baris`,`catatan_khusus`) values (1,1,3,25000.00,2,50000.00,'Less sugar'),(2,1,11,22000.00,1,22000.00,NULL),(3,2,4,22000.00,1,22000.00,'Extra ice'),(4,2,12,15000.00,1,15000.00,NULL),(5,3,8,30000.00,1,30000.00,'No onion'),(6,3,9,28000.00,1,28000.00,NULL),(7,3,7,18000.00,2,36000.00,'Less ice'),(8,4,2,20000.00,2,40000.00,NULL),(9,4,10,20000.00,1,20000.00,NULL),(10,5,5,27000.00,1,27000.00,NULL),(11,5,1,18000.00,1,18000.00,NULL),(12,6,2,20000.00,1,20000.00,'Less sugar'),(13,7,6,25000.00,1,25000.00,NULL),(14,7,1,18000.00,1,18000.00,NULL),(15,8,2,20000.00,1,20000.00,NULL);

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(25) NOT NULL,
  `user_id` int unsigned NOT NULL,
  `tipe_pesanan` enum('dine_in','takeaway') NOT NULL,
  `nomor_meja` varchar(10) DEFAULT NULL,
  `nama_pemesan` varchar(100) DEFAULT NULL,
  `total_subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pajak_nominal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `diskon_nominal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metode_bayar` enum('tunai','qris','transfer','debit') NOT NULL,
  `uang_dibayar` decimal(12,2) NOT NULL DEFAULT '0.00',
  `uang_kembali` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('diproses','selesai','dibatalkan') NOT NULL DEFAULT 'diproses',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_invoice` (`invoice_number`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_created` (`created_at`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `orders` */

insert  into `orders`(`id`,`invoice_number`,`user_id`,`tipe_pesanan`,`nomor_meja`,`nama_pemesan`,`total_subtotal`,`pajak_nominal`,`diskon_nominal`,`grand_total`,`metode_bayar`,`uang_dibayar`,`uang_kembali`,`status`,`created_at`) values (1,'INV-POS-20261115-0001',2,'dine_in','3',NULL,72000.00,7200.00,0.00,79200.00,'tunai',80000.00,800.00,'selesai','2026-11-15 10:15:00'),(2,'INV-POS-20261116-0001',2,'takeaway',NULL,NULL,37000.00,3700.00,0.00,40700.00,'qris',40700.00,0.00,'selesai','2026-11-16 09:30:00'),(3,'INV-POS-20261116-0002',2,'dine_in','5',NULL,94000.00,9400.00,5000.00,98400.00,'tunai',100000.00,1600.00,'selesai','2026-11-16 12:45:00'),(4,'INV-POS-20261117-0001',2,'dine_in','2',NULL,60000.00,6000.00,0.00,66000.00,'debit',66000.00,0.00,'selesai','2026-11-17 08:50:00'),(5,'INV-POS-20261117-0002',2,'takeaway',NULL,NULL,45000.00,4500.00,0.00,49500.00,'tunai',50000.00,500.00,'diproses','2026-11-17 09:20:00'),(6,'INV-POS-20261117-0003',1,'dine_in','1',NULL,20000.00,2000.00,0.00,22000.00,'tunai',30000.00,8000.00,'selesai','2026-11-17 10:49:41'),(7,'INV-POS-20261117-0004',1,'dine_in','2',NULL,43000.00,4300.00,0.00,47300.00,'qris',47300.00,0.00,'selesai','2026-11-17 10:50:35'),(8,'INV-POS-20261117-0005',1,'dine_in','1',NULL,20000.00,2000.00,0.00,22000.00,'tunai',50000.00,28000.00,'selesai','2026-11-17 11:35:35');

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `kode_produk` varchar(20) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL,
  `harga_pokok` decimal(12,2) NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `unit` varchar(20) NOT NULL DEFAULT 'porsi',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_kode` (`kode_produk`),
  KEY `idx_products_category` (`category_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`category_id`,`kode_produk`,`nama_produk`,`harga_jual`,`harga_pokok`,`stok`,`unit`,`created_at`) values (1,1,'COF-001','Espresso',18000.00,7000.00,49,'cup','2026-11-17 10:46:50'),(2,1,'COF-002','Americano',20000.00,8000.00,48,'cup','2026-11-17 10:46:50'),(3,1,'COF-003','Cafe Latte',25000.00,11000.00,40,'cup','2026-11-17 10:46:50'),(4,1,'COF-004','Kopi Susu Titik Temu',22000.00,9000.00,60,'cup','2026-11-17 10:46:50'),(5,2,'NCF-001','Matcha Latte',27000.00,12000.00,30,'cup','2026-11-17 10:46:50'),(6,2,'NCF-002','Chocolate',25000.00,10000.00,29,'cup','2026-11-17 10:46:50'),(7,2,'NCF-003','Lemon Tea',18000.00,6000.00,45,'cup','2026-11-17 10:46:50'),(8,3,'FOD-001','Nasi Goreng Kampung',30000.00,14000.00,25,'porsi','2026-11-17 10:46:50'),(9,3,'FOD-002','Mie Goreng',28000.00,12000.00,25,'porsi','2026-11-17 10:46:50'),(10,3,'FOD-003','French Fries',20000.00,8000.00,35,'porsi','2026-11-17 10:46:50'),(11,4,'PST-001','Croissant',22000.00,10000.00,6,'pcs','2026-11-17 10:46:50'),(12,4,'PST-002','Donut Cokelat',15000.00,6000.00,4,'pcs','2026-11-17 10:46:50');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','kasir') NOT NULL DEFAULT 'kasir',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`password_hash`,`nama_lengkap`,`role`,`status`,`created_at`) values (1,'admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Administrator Kafe','admin','aktif','2026-11-17 10:46:23'),(2,'kasir','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Kasir Titik Temu','kasir','aktif','2026-11-17 10:46:23');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
