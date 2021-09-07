#Configuration

преопределение штатного класса сущности

    security:
        firewall_session_key: main - имя файрвола для восстановления сессии
        redirect_by_server: false  - включение/выключение перенаправление сервером или фронтом в случае успеха
        route:
            check: login_check  - маршрут проверки авторизации
            login: login        - стартовая страница
            redirect: core_home - перенаправление в случае успеха
        event:
            on_authentication_success: true   - включение/выключение события на успешную авторизацию
            on_authentication_failure: ~      - включение/выключение события на успешную авторизацию
        #настройки сервров АД
        ldap_servers:
            server.ru:
                - { host: "ldap://my0.server0.ru", port: "636", search: "OU=MSK,DC=server0,DC=ru" }
                - { host: "ldap://my1.server1.ru", port: "636", search: "OU=MSK,DC=server1,DC=ru" }
#Events

два события on_authentication_success и on_authentication_failure

    ...
    class AuthenticationSuccessEventSubscriber implements EventSubscriberInterface
    {
        public function onAuthenticationSuccessEvent(AuthenticationSuccessEvent $event)
        {
                $event->setResponse(['accessToken' => 1, 'refreshTokent' => 2]);
        }

        public static function getSubscribedEvents()
        {
            return [    AuthenticationSuccessEvent::class => 'onAuthenticationSuccessEvent',    ];
        }

    }
  
#Тесты:
    
/**
composer install --dev
/usr/bin/php vendor/phpunit/phpunit/phpunit --bootstrap tests/bootstrap.php --configuration phpunit.xml.dist tests --teamcity
*/

