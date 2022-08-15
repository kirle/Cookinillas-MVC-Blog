DROP DATABASE IF EXISTS `mvcblog`;

create database mvcblog;
use mvcblog;

DROP USER IF EXISTS 'tsw'@'localhost';
CREATE USER 'tsw'@'localhost' IDENTIFIED BY 'tsw';
GRANT ALL PRIVILEGES ON *.* TO 'tsw'@'localhost' IDENTIFIED BY 'tsw';

create table users (
		email varchar(255),
		alias varchar(255),
		passwd varchar(255),
		primary key (alias)
) ENGINE=INNODB DEFAULT CHARACTER SET = utf8;

create table recipes (
	id int auto_increment,
	title varchar(255),
	cooking_time int not null,
	content text,
	author varchar(255) not null,
	image_url varchar(255),
	date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	primary key (id),
	foreign key (author) references users(alias)
) ENGINE=INNODB DEFAULT CHARACTER SET = utf8;

create table ingredient(
	id int auto_increment,
	name varchar(255),
	primary key (id)
) ENGINE=INNODB DEFAULT CHARACTER SET = utf8;



create table recipeIngredients(
	id int auto_increment,
	recipe_id int not null,
	ingredient_id int not null,
	amount int not null,
	primary key (id),
	foreign key (recipe_id) references recipes(id) on delete cascade,
	foreign key (ingredient_id) references ingredient(id) on delete cascade
)ENGINE=INNODB DEFAULT CHARACTER SET = utf8;


create table comments (
	id int auto_increment,	 
	content varchar(255),
	author varchar(255) not null,
	recipe int not null,
	primary key (id),
	foreign key (author) references users(alias),
	foreign key (recipe) references recipes(id) on delete cascade
) ENGINE=INNODB DEFAULT CHARACTER SET = utf8;

create table favs(
	id int auto_increment,
	author varchar(255) not null,
	recipe_id int not null,
	primary key (id),
	foreign key (author) references users(alias),
	foreign key (recipe_id) references recipes(id) on delete cascade
) ENGINE=INNODB DEFAULT CHARACTER SET = utf8;

/* Some example inserts */
INSERT INTO `users`(`email`, `alias`, `passwd`) VALUES ("test1@gmail.com","test1","test1");
INSERT INTO `ingredient`(`name`) VALUES ("pimiento");
INSERT INTO `ingredient`(`name`) VALUES ("tomate");
INSERT INTO `ingredient`(`name`) VALUES ("lechuga");
INSERT INTO `ingredient`(`name`) VALUES ("cebolla");

INSERT INTO `recipes`(`title`, `cooking_time`, `content`, `author`, `image_url`, `date`) VALUES ("TestRecipe",10,"Esta receta es solo una receta de prueba para insertar en la base de datos durante la ejecución del script de creación de la misma. Puede que la imagen no cargue y no tiene ingredientes, pero a tus recetas si que le puedes añadir ingredientes. Y esto viene siendo un lorem ipsum personalizado para la misma. Puedo aprovecharlo para comentar cosas de la entrega. Esta primera la hice con más tiempo (¿y cariño?) que la segunda, y aun que resulta caótica en algunos aspectos donde se nota la inexperiencia, siendo esta la segunda web que hago en toda la carrera (el css por ejemplo, que resulta bastante caótico) ha sido entretenido y didáctico. Fin del lorem ipsum" ,'test1','https://truffle-assets.imgix.net/1t1bxm43v4e3_6L7mosrpNCkwUYWq2s8eaK_pollo-al-ajillo_landscapeThumbnail_en-US.jpeg', CURRENT_DATE);


