**If new to Laravel**

1. Install Composer

1. Run Composer install in the root repository directory to install dependencies

Here's the main files:

* Router: /routes/api.php
* Controller: app/Http/Controllers/SearchController.php
* Validation: app/Http/Requests/SearchRequest.php
* Main Logic: app/Services/SearchFacade.php

**The endpoint**
domain.com/api/search?checkin=29-04-2017&checkout=03-05-2017&destination=singapore&guests=3&suppliers=supplier1,supplier2