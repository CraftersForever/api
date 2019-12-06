# Collection of APIs hosted by CraftersForever

Following APIS are currently hosted here:

## names.php:
A php script that returns the history of a Minecraft players usernames. Input may be the UUID or the username of the player.  Supports CORS.

A demo of this file can be viewed on https://api.craftersforever.de/names.php
Working examples are:
- https://api.craftersforever.de/names.php?uuid=BloodSKreaper
- https://api.craftersforever.de/names.php?uuid=14636f4d3ea94544a7837e6d14524a5c
- https://api.craftersforever.de/names.php?uuid=14636f4d-3ea9-4544-a783-7e6d14524a5c

## playtime.php
A php script that returns the playtime of a specific player on the CraftersForever minecraft server. A GET-Parameter has to be provided, filled with the v4UUID with slashes of a player. Returns 0 if the player was never seen before on the server. Else it will return the playtime of a player in minutes.


## status.php
A php script that returns the current state of the CraftersForever minecraft server. 
