![Logo](app/public/img/munition-text-logo.png)

The Munition Framework is an MVC framework built in PHP.
It is designed for rapid development, easy to understand and use features, and minimal configuration.

_____________

## Getting Started

### System Requirements

 * PHP v5.4 or higher
 * Access to RewriteRules on webserver


### Installing

#### Step One - Retrieve Munition(s)!

Simply fetch the latest version of the Munition Framework from GitHub.
If you want stable version, use the `latest-release` tag, otherwise just use the master branch.


#### Step Two - Load!

Either configure the neccesary configurations yourself, or let Munition help you.
Browse to where you installed Munition to with your webbrowser, and the default App should list what needs to be done before you can start developing.

#### Step Three - Fire!

You now have a small, fast and easy to use PHP framework that can be used to develop webapps, RESTful API's, backend servers, you name it.

To clean up from installation, simply remove the default install application by deleting:
`app/controllers/install_controller.php`
`app/templates/header.php`
`app/templates/footer.php`
`app/templates/index.php`

Then clear out the constructor function in the AppRouter, located at:
`config/routes.php`

