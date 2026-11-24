CREATE TABLE menu_items (
    path        VARCHAR(255) PRIMARY KEY,
    label       VARCHAR(80)  NOT NULL,
    title       VARCHAR(120) NOT NULL,
    description VARCHAR(255) NOT NULL,
    template    VARCHAR(64)  NOT NULL,
    sort_order  INTEGER      NOT NULL UNIQUE CHECK (sort_order >= 0),
    is_visible  BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (path LIKE '/%'),
    CHECK (LENGTH(label) > 0)
);

CREATE INDEX idx_menu_items_visible_order ON menu_items (is_visible, sort_order);
