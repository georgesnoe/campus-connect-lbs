create table utilisateurs (
    id int not null auto_increment,
    nom varchar(255) not null,
    nom varchar(255) not null,
    email varchar(255) not null,
    matricule varchar(255) not null,
    mot_de_passe varchar(255) not null,
    date_heure_creation timestamp  not null default current_timestamp,
    primary key (id),
);

create table messages (
    id int not null auto_increment,
    id_expediteur int not null,
    id_destinataire int not null,
    message varchar(255) not null,
    date_heure_envoi timestamp  not null default current_timestamp,
    primary key (id),
    foreign key (id_expediteur) references utilisateurs(id),
    foreign key (id_destinataire) references utilisateurs(id)
);
