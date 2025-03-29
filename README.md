#   Astudio Filter System

This Laravel application provides a platform for managing job listings with advanced filtering capabilities.

##   Setup Instructions

1.  Clone the repository.
2.  Install dependencies: `composer install`
3.  Create a copy of the `.env.example` file and name it `.env`.
4.  Configure your database connection in the `.env` file.
5.  Generate an application key: `php artisan key:generate`
6.  Run database migrations: `php artisan migrate`
7.  Seed the database with initial data (optional): `php artisan db:seed`
8.  Start the development server: `php artisan serve`

##   API Endpoints

* `GET /api/jobs`: Retrieves a list of jobs, with optional filtering.

##   Filtering Jobs

The `/api/jobs` endpoint supports advanced filtering of job listings via the `filter` query parameter. The filtering syntax allows for combining multiple conditions with logical operators and grouping.

###   Filter Syntax

The general structure of the filter query parameter is:

/api/jobs?filter=(condition1) AND/OR (condition2) AND/OR ...


* **Conditions:** Individual filtering criteria.
* **Logical Operators:** `AND`, `OR` to combine conditions.
* **Grouping:** Parentheses `()` to control the order of operations.

###   Condition Types and Operators

####   1.  Basic Field Filtering

These filters apply to standard job fields.

* **Text/String Fields** (`title`, `description`, `company_name`):
    * `=` , `!=`: Equality operators.
    * `LIKE`: Substring matching (e.g., `title LIKE %Engineer%`).
* **Numeric Fields** (`salary_min`, `salary_max`):
    * `=` , `!=`: Equality operators.
    * `>`, `<`, `>=`, `<=`: Comparison operators.
* **Boolean Fields** (`is_remote`):
    * `=` , `!=`: Equality operators (use `1` for true, `0` for false).
* **Enum Fields** (`job_type`, `status`):
    * `=` , `!=`: Equality operators.
    * `IN`: Match any of a set of values (e.g., `job_type IN (full-time, part-time)`).
* **Date Fields** (`published_at`, `created_at`, `updated_at`):
    * `=` , `!=`: Equality operators.
    * `>`, `<`, `>=`, `<=`: Comparison operators.

####   2.  Relationship Filtering

These filters apply to the relationships of the `Job` model.

* Supported Relationships: `languages`, `locations`, `categories`
* Operators:
    * `=`: Exact match.
    * `HAS_ANY`: Job has any of the specified related values.
    * `IS_ANY`: Relationship matches any of the specified values (often behaves the same as `HAS_ANY`).
    * `EXISTS`: Relationship exists (job has at least one related record).

####   3.  EAV Attribute Filtering

These filters apply to the dynamic attributes of jobs.

* Operator support varies based on the attribute's `type`:
    * **Text Attributes:** `=`, `!=`, `LIKE`
    * **Number Attributes:** `=`, `!=`, `>`, `<`, `>=`, `<=`
    * **Boolean Attributes:** `=`, `!=`
    * **Select Attributes:** `=`, `!=`, `IN`

###   Filter Examples

* Simple:
    * `/api/jobs?filter=title LIKE Night`
    * `/api/jobs?filter=salary_min>60000`
* Combined with AND:
    * `/api/jobs?filter=job_type=full-time AND salary_min>50000`
* Relationship filtering:
    * `/api/jobs?filter=languages HAS_ANY (PHP,JavaScript)`
    * `/api/jobs?filter=locations IS_ANY (New York,Remote)`
    * `/api/jobs?filter=categories EXISTS()`
* EAV attribute filtering:
    * `/api/jobs?filter=attribute:years_experience>=3`
    * `/api/jobs?filter=attribute:degree_required IN (Bachelor,Master)`
* Complex example:
    * `/api/jobs?filter=(job_type=full-time AND (languages HAS_ANY (PHP,JavaScript))) AND (locations IS_ANY (New York,Remote)) AND attribute:years_experience>=3 AND salary_min!=100`

##   JobFilterService Class

The `JobFilterService` class handles the parsing and application of these filters to Eloquent queries.

###   Key Methods

* `__construct(Request $request)`: Initializes the service with the incoming request, extracting the `filter` query parameter.
* `parseToJson(string $filterString)`: Parses the filter string into a nested array structure that represents the conditions, operators, and grouping. This method handles parentheses and `AND`/`OR` logic.
* `processCondition(string $condition)`: Parses a single filter condition string to extract the key, operator, and value. It uses regular expressions to identify different condition patterns.
* `parseConditionString(string $conditionString)`: Parses a nested condition string (the part within parentheses).
* `defineFilterType(string $name)`: Determines the type of filter being applied (field, relationship, or attribute).
* `fieldType(string $fieldName)`: Determines the data type of a given field (string, number, boolean, enum, date).
* `applyFilters(array $filters)`: Applies the parsed filter conditions to the Eloquent query. This is the main method that constructs the `where` and `whereHas` clauses.
* `fieldFilter(array $filter)`: Applies a filter to a standard job field.
* `relationshipFilter(array $filter)`: Applies a filter to a relationship (e.g., `languages`, `locations`).
* `attributeFilter(array $filter)`: Applies a filter to an EAV attribute.

###   Design Choices and Assumptions

* The service is designed to be flexible and extensible, allowing for the addition of new filter types and operators.
* It assumes a specific structure for the database tables and relationships.
* Error handling is implemented by throwing exceptions for invalid filter syntax or data.
* The code prioritizes readability and maintainability, with clear method names and comments.
* A `JobResource` class is used to format the API response for job data, ensuring consistency and controlling data exposure.

###   Performance Optimizations

* **Indexing:** To optimize database query performance, especially for filtering, the following indexes are recommended:
    * **`jobs` table:** Indexes on `title`, `company_name`, `salary_min`, `salary_max`, `is_remote`, `job_type`, `status`, and `published_at`.
    * **Many-to-many pivot tables** (`job_language`, `job_location`, `job_category`): Composite indexes on `job_id` and the related model's ID (e.g., `language_id`).
    * **`attributes` table:** Index on `name`.
    * **`job_attribute_values` table:** Indexes on `job_id`, `attribute_id`, and a composite index on `job_id` and `attribute_id`. Consider an index on `value` depending on data type and cardinality.
* **Eager Loading:** The `applyFilters` method supports eager loading of relationships via the `$with` parameter. This prevents the N+1 query problem and significantly improves performance when accessing related data (e.g., `languages`, `locations`, `categories`). Use eager loading strategically based on your API's usage patterns.


