### Basic CQRS and Event Sourcing with Prooph

This is an example application aimed at teaching basic event sourcing.

#### Requirements

To run this application, you will need:

 * [PHP 7](https://secure.php.net/downloads.php)
 * [composer](https://getcomposer.org/)
 * [ext-pdo](http://php.net/manual/en/book.pdo.php)
 * [ext-pdo_sqlite](http://php.net/manual/en/ref.pdo-sqlite.php)

#### Toolchain

The tools that we are going to use are:

 * [Zend Expressive](https://github.com/zendframework/zend-expressive) a simple PSR-7/HTTP routing framework
 * [Prooph Components](https://github.com/prooph/) abstraction for common CQRS + Event Sourcing concerns

#### Domain of the app

The domain of the application is quite limited, but sufficient to explain how and when to effectively use
CQRS and EventSourcing.

The MVP of the application has following specification:

 * assume that each person interacting with the system has a badge with a username on it
 * assume that the username is given: we assume that the input data is already validated against existing users
 * track people entering (check-in) a building
 * track people leaving (check-out) a building
 * prevent people from double-entering a building (security concern)
 * prevent people from double-leaving a building (security concern)
 * allow querying a list of people that are currently in the building

#### Build steps

Following steps are to be implemented:

- [x] Ability to register a new building (already provided)
- [ ] Ability to check-in with a username and a building identifier (skeleton code provided)
- [ ] Ability to check-out with a username and a building identifier (skeleton code provided)
- [ ] Provide console output (STDERR) every time a check-in happens (event handler)
- [ ] Provide console output (STDERR) every time a check-out happens (event handler)
- [ ] Provide a file per building (accessible via HTTP) with usernames of currently checked-in persons
