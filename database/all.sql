create schema cars;
use cars;

create table cars
(
    id                bigint unsigned auto_increment
        primary key,
    brand             varchar(255)              not null,
    model             varchar(255)              not null,
    submodel          varchar(255)              not null,
    transmission      varchar(255)              not null,
    traction          varchar(255)              not null,
    fuel              varchar(255)              not null,
    km                int                       not null,
    stock_type        varchar(255)              not null,
    price_without_vat int                       not null,
    interior_color    varchar(255)              not null,
    exterior_color    varchar(255)              not null,
    created_at        timestamp default (now()) null,
    updated_at        timestamp default (now()) null
)
    collate = utf8mb4_unicode_ci;

create table sellers
(
    id         bigint unsigned auto_increment
        primary key,
    name       varchar(255)              not null,
    created_at timestamp default (now()) null,
    updated_at timestamp default (now()) null
)
    collate = utf8mb4_unicode_ci;

create table leads
(
    id                     bigint unsigned auto_increment
        primary key,
    seller_id              bigint unsigned                     not null,
    seller_start_timestamp timestamp default CURRENT_TIMESTAMP null,
    email                  varchar(255)                        not null,
    first_name             varchar(255)                        not null,
    last_name              varchar(255)                        not null,
    phone                  varchar(255)                        not null,
    message                text                                not null,
    created_at             timestamp default (now())           null,
    updated_at             timestamp default (now())           null,
    constraint leads_email_unique
        unique (email),
    constraint leads_phone_unique
        unique (phone),
    constraint leads_seller_id_foreign
        foreign key (seller_id) references sellers (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

