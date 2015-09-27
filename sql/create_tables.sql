CREATE TABLE Users(
    id SERIAL PRIMARY KEY,
    name varchar(50) NOT NULL,
    password varchar(50) NOT NULL
);

CREATE TABLE League(
    id SERIAL PRIMARY KEY,
    name varchar(50) NOT NULL,
    game varchar(50) NOT NULL,
    platform varchar(50) NOT NULL,
    user_id INTEGER NOT NULL,
    rules VARCHAR(5000),
    info VARCHAR(5000)
);

CREATE TABLE Driver (
    id SERIAL PRIMARY KEY,
    league_id INTEGER NOT NULL,
    name varchar(50) NOT NULL,
    car varchar(50) NOT NULL,
    team_id INTEGER
);

-- CREATE TABLE Team (
--     id SERIAL PRIMARY KEY,
--     league_id INTEGER NOT NULL,
--     name varchar(50),
-- );

CREATE TABLE Race (
    id SERIAL PRIMARY KEY,
    league_id INTEGER NOT NULL,
    track varchar(50) NOT NULL,
    raceday Date NOT NULL,
    laps INTEGER NOT NULL
);


CREATE Table Rslt (
    id SERIAL PRIMARY KEY,
    league_id INTEGER NOT NULL,
    race_id INTEGER NOT NULL,
    driver_id INTEGER NOT NULL,
    quali_time INTEGER,
    race_time INTEGER,
    quali_extra varchar(50),
    race_extra varchar(50),
    laps INTEGER
);