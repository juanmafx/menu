import pathlib
import sqlite3
import sys

schema = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8")
connection = sqlite3.connect(":memory:")
connection.executescript(schema)
insert = "INSERT INTO menu_items (path, label, title, description, template, sort_order) VALUES (?, ?, ?, ?, ?, ?)"
connection.execute(insert, ("/", "Home", "Home", "Landing page", "home", 0))
for invalid in (("about", "About", "About", "No slash", "about", 1), ("/x", "", "X", "Empty label", "x", 2), ("/y", "Y", "Y", "Duplicate order", "y", 0)):
    try:
        connection.execute(insert, invalid)
    except sqlite3.IntegrityError:
        continue
    sys.exit(f"constraint not enforced for {invalid[0]}")
print("schema verified on SQLite", sqlite3.sqlite_version)
