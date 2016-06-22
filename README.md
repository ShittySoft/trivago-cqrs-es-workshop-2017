### Basic CQRS and Event Sourcing with Prooph

This is an example application aimed at teaching basic event sourcing.

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

The application has to be able to reject people

Interactive part of the process:

- [ ] Create the middleware to `/result` page
- [ ] Create a projector to save export information to a file, counting how much move was made to complete the game.
- [ ] Trigger a new domain event like `ReportResultWasExported`
- [ ] Send a fake mail when `ReportResultWasExported`
