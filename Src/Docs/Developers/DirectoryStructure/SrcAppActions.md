# Actions
It is used to define actions which can be accessed throw Actions Controller. Code Sample:

```
<?php 

namespace App\Actions;

use App\Core\Controller;

class ActionName extends Controller {
    
    public function __construct(){
        parent::__construct();

        //Check if user is logged in or on local
        $this->app->user->checkAuthentication();
        
    }

    public function index($id, $params){
        
    }
}

```

If you want to access this action you can use `http://localhost/actions/ActionName`.