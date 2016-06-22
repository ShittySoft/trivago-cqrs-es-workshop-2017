### Basic CQRS and Event Sourcing with Prooph

This is an example application aimed at teaching basic event sourcing.

#### Toolchain

The tools that we are going to use are:

 * [Zend Expressive](https://github.com/zendframework/zend-expressive) a simple PSR-7/HTTP routing framework
 * [Prooph Components](https://github.com/prooph/) abstraction for common CQRS + Event Sourcing concerns


Interactive part of the process:

- [ ] Create the middleware to `/result` page
- [ ] Create a projector to save export information to a file, counting how much move was made to complete the game.
- [ ] Trigger a new domain event like `ReportResultWasExported`
- [ ] Send a fake mail when `ReportResultWasExported`
