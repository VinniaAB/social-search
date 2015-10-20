CREATE TABLE "vss_media" (
  "vss_media_id" int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  "source" tinyint not null,
  "original_id" varchar(100) not null,
  "text" text,
  "images" text,
  "videos" text,
  "lat" float,
  "long" float,
  "username" varchar(100) not null,
  "created_at" int(11) NOT NULL
);

create unique index vss_media_source_original_id_idx on vss_media("source", "original_id");
create index vss_media_created_at_idx on vss_media("created_at");
create index vss_media_lat_idx on vss_media("lat");
create index vss_media_long_idx on vss_media("long");

create table "vss_tag" (
  "vss_tag_id" int primary key not null auto_increment,
  "name" varchar(100) not null,
  "vss_media_id" int not null,
  foreign key (vss_media_id) REFERENCES vss_media("vss_media_id")
    on UPDATE CASCADE
    on DELETE CASCADE
);

create index vss_tag_name_idx on vss_tag("name");
