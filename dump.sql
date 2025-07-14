create table users
(
	id int unsigned auto_increment
		primary key,
	email varchar(128) not null,
	name varchar(128) not null,
	password varchar(32) not null,
	created_at timestamp default current_timestamp() not null,
	updated_at timestamp default current_timestamp() not null on update current_timestamp(),
	constraint users_pk_2
		unique (email)
)
comment 'Cadastro de usuarios';

create table user_sessions
(
	id int unsigned auto_increment
		primary key,
	user int unsigned not null,
	secret varchar(255) null,
	ip varchar(45) null,
	ttl int(6) unsigned not null comment 'Tempo de vida em segundos',
	created_at timestamp default current_timestamp() not null comment 'Quando a sessão foi criada',
	expires_at timestamp not null comment 'Quando a sessão irá expirar',
	constraint user_sessions_users_id_fk
		foreign key (user) references users (id)
			on update cascade on delete cascade
);


