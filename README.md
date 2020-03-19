1. Add the package in your composer.json and run composer install

require: {
        ....
        "mach/nwl-bundle": "1.*",
        ....
}



2. Register the bundle in AppKernel

public function registerBundles()
{
    $bundles = array(
        ...
        new Mach\Bundle\NotificationBundle\MachNwlBundle(),
        ...
    );
}


3. Register the entity MachNotificationBundle in your default entity manager

orm:
    entity_managers:
        default:
            connection:       default
            mappings:
                ...
                MachNwlBundle: ~
                ...

