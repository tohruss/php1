<?php

namespace Providers;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Src\Provider\AbstractProvider;

class DBProvider extends AbstractProvider
{
    private Capsule $dbManager;

    public function register(): void
    {
        $this->dbManager = new Capsule();
    }

    public function boot(): void
    {
        $this->dbManager->addConnection($this->app->settings->getDbSetting());
        $this->dbManager->setEventDispatcher(new Dispatcher(new Container));
        $this->dbManager->setAsGlobal();
        $this->dbManager->bootEloquent();

        $this->app->bind('db', $this->dbManager);
    }

}
