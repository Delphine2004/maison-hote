// Créer un utilisateur admin
db.getSiblingDB("admin").createUser({
    user: "admin",
    pwd: "adminpassword",
    roles: [{ role: "root", db: "admin" }],
});

// Créer un utilisateur spécifique
db.getSiblingDB("guesthouse").createUser({
    user: "guesthouse_user",
    pwd: "guesthouse25",
    roles: [{ role: "readWrite", db: "guesthouse" }],
});
