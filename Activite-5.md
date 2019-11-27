La table action_log est mise à jour mais le status de l'utilisateur
n'est pas modifié.
Cela est du à l'exception qui stoppe le programme après la modification de la
table action_log.

Pour eviter ça il faudrait utiliser un rollback.
