create database BDM_Capa;

use BDM_capa;

create table users
(
    id       int auto_increment
        primary key,
    name     varchar(50)  null,
	password varchar(100) not null,
    username varchar(20),
    email    varchar(100) not null
);

create table notes
(
    id         int auto_increment
        primary key,
    title      varchar(100) not null,
    note       text         not null,
    user_id    int          null,
    create_at  timestamp    null,
    updated_at timestamp    null,
    deleted_at timestamp    null,
    constraint notes_users_id_fk
        foreign key (user_id) references users (id)
);

drop table users;
drop table notas;
