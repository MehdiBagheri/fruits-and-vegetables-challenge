# 🍎🥕 Fruits and Vegetables

## Requirements
* PHP >=8.2
* Symfony cli
* Composer

## 🎯 Goal
We want to build a service which will take a `request.json` and:
* Process the file and create two separate collections for `Fruits` and `Vegetables`
* Each collection has methods like `add()`, `remove()`, `list()`;
* Units have to be stored as grams;
* Store the collections in a storage engine of your choice. (e.g. Database, In-memory)
* Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
* Provide another API endpoint to add new items to the collections (i.e., your storage engine).
* As a bonus you might:
    * consider giving an option to decide which units are returned (kilograms/grams);
    * how to implement `search()` method collections;
    * use latest version of Symfony's to embed your logic 

## Implemented
* [x] ArrayCollection is added to process the request.json
  * Collection is immutable, which provides the following methods:
    * `getIterator()`
    * `get(key)`
    * `add(element)`
    * `remove(key)`
    * `list()`
    * `reduce(callback, initialValue)`
    * `count()`
* [x] The file `requst.json` is processed and the collections are created and are persisted in the storage engine.(sqlite)
* [x] API endpoints
  * Three endpoints for list, add and retrieve `fruits` is added with filter options
    * `GET`:
      * `api/v1/fruits`: Will return a list of the fruits with default unit in grams.
      * `api/v1/fruits?filter=apple`: Will return a list of the fruits filtered by search term and unit in grams.
      * `api/v1/fruits?filter=apple&unit=kg`: Will return a list of the fruits filtered by search term (filter by name) and unit in kilograms.
      * `api/v1/fruits?unit=kg`: Will return a list of the fruits with unit in kilograms.
    * `POST`:
      * `api/v1/fruits`: Will create fruit and return the created fruit.
  * Three endpoint for list, add and retrieve `vegetables` is added with filter options
    * `GET`:
    * `api/v1/vegetables`: Will return a list of the vegetables with default unit in grams.
    * `api/v1/vegetables?filter=carrot`: Will return a list of the vegetables filtered by search term and unit in grams.
    * `api/v1/vegetables?filter=carrot&unit=kg`: Will return a list of the vegetables filtered by search term (filter by name) and unit in kilograms.
    * `api/v1/vegetables?unit=kg`: Will return a list of the vegetables with unit in kilograms.
    * `POST`:
      * `api/v1/vegetables`: Will create vegetable and return the created vegetable.
