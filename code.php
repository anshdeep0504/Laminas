<?php

use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\ServiceManager\ServiceManager;

require_once __DIR__ . '/../vendor/autoload.php';

// Define the application configuration
$config = [
    'modules' => [
        'Laminas\Mvc',
        'Laminas\View',
        'Laminas\Router',
        'Laminas\Form',
        'Laminas\I18n',
    ],
    'module_listener_options' => [
        'module_paths' => ['./module'],
    ],
    'service_manager' => [
        'factories' => [
            'Laminas\ServiceManager\Factory\InvokableFactory' => 'Laminas\ServiceManager\Factory\InvokableFactory',
        ],
    ],
];

// Create the Laminas application instance
$application = Application::init($config);

// Routing logic (home, about, contact form)
$router = $application->getServiceManager()->get('Router');
$router->addRoute(
    'home',
    new Literal([
        'route'    => '/',
        'defaults' => [
            'controller' => 'Home\Controller\Index',
            'action'     => 'index',
        ],
    ])
);
$router->addRoute(
    'about',
    new Literal([
        'route'    => '/about',
        'defaults' => [
            'controller' => 'Home\Controller\About',
            'action'     => 'index',
        ],
    ])
);
$router->addRoute(
    'contact',
    new Segment([
        'route'    => '/contact[/:action]',
        'defaults' => [
            'controller' => 'Home\Controller\Contact',
            'action'     => 'index',
        ],
    ])
);

// Controller definitions
class HomeController
{
    public function indexAction()
    {
        return [
            'title' => 'Welcome to Laminas Framework',
            'features' => [
                ['icon' => 'fa-cogs', 'title' => 'Modular & Scalable', 'description' => 'Easy to scale your application with reusable components.'],
                ['icon' => 'fa-shield-alt', 'title' => 'Built-in Security', 'description' => 'Laminas provides security features out-of-the-box.'],
                ['icon' => 'fa-plug', 'title' => 'Integration Ready', 'description' => 'Easily integrate with external systems like APIs, databases, and third-party services.']
            ]
        ];
    }
}

class AboutController
{
    public function indexAction()
    {
        return [
            'title' => 'About Laminas',
            'content' => 'Laminas is an open-source enterprise-level PHP framework for building web applications.'
        ];
    }
}

class ContactController
{
    public function indexAction()
    {
        return [
            'title' => 'Contact Us',
            'form'  => new ContactForm()
        ];
    }

    public function submitAction()
    {
        // Handle form submission
        // Validate and process the contact form
        return ['status' => 'Message Sent'];
    }
}

// Contact Form (Using Laminas\Form)
class ContactForm extends Laminas\Form\Form
{
    public function __construct($name = null)
    {
        parent::__construct('contact');
        $this->add([
            'name' => 'name',
            'type' => 'Text',
            'options' => ['label' => 'Name'],
            'attributes' => ['required' => 'required'],
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'Email',
            'options' => ['label' => 'Email'],
            'attributes' => ['required' => 'required'],
        ]);
        $this->add([
            'name' => 'message',
            'type' => 'Textarea',
            'options' => ['label' => 'Message'],
            'attributes' => ['required' => 'required'],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => ['value' => 'Send Message'],
        ]);
    }
}

// Rendering the view
$controller = new HomeController();
$data = $controller->indexAction();
$aboutController = new AboutController();
$aboutData = $aboutController->indexAction();
$contactController = new ContactController();
$contactData = $contactController->indexAction();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $data['title'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        header { background: #4CAF50; color: white; padding: 10px 0; text-align: center; }
        section { padding: 20px; }
        .features { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .feature { background: white; padding: 20px; width: 200px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center; }
        .feature i { font-size: 3em; margin-bottom: 10px; }
        footer { background: #333; color: white; padding: 10px 0; text-align: center; }
        footer a { color: #ddd; text-decoration: none; }
        form { max-width: 500px; margin: 0 auto; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>

    <header>
        <h1><?= $data['title'] ?></h1>
    </header>

    <section>
        <h2>Features</h2>
        <div class="features">
            <?php foreach ($data['features'] as $feature): ?>
                <div class="feature">
                    <i class="fas <?= $feature['icon'] ?>"></i>
                    <h3><?= $feature['title'] ?></h3>
                    <p><?= $feature['description'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h2>About Laminas</h2>
        <p><?= $aboutData['content'] ?></p>
    </section>

    <section>
        <h2>Contact Us</h2>
        <form action="/contact/submit" method="POST">
            <label for="name">Name</label>
            <input type="text" name="name" required>

            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="message">Message</label>
            <textarea name="message" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </section>

    <footer>
        <p>Powered by <a href="https://getlaminas.org" target="_blank">Laminas Project</a></p>
    </footer>

</body>
</html>
