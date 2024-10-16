DROP TABLE IF EXISTS url_checks;
DROP TABLE IF EXISTS urls;

CREATE TABLE urls(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL UNIQUE
);

CREATE TABLE url_checks(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    url_id BIGINT REFERENCES urls(id),
    status_code INTEGER NOT NULL,
    h1 VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL UNIQUE
);

INSERT INTO urls (name, created_at) VALUES ('http://google.com', '2024-10-14 10:00:00');
INSERT INTO url_checks
    (url_id, status_code, h1, title, description, created_at)
VALUES
    (1, 200, '', '', '', '2024-10-14 10:00:00'),
    (1, 200, '', '', '', '2024-10-14 11:00:00'),
    (1, 200, '', '', '', '2024-10-14 12:00:00')
;