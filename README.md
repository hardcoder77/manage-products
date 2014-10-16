Products API
===============


Basic useful feature list:

 * RESTful API to:
	1. Create a Product
    2. Get a Product
    3. Query for a Product by its attributes
    4. Delete a Product
 * Only authenticated users can take the above actions on the products.
 


Setup
---
####For Ubuntu:
    bash <(curl -s https://raw.githubusercontent.com/hardcoder77/manage-products/master/setup/setup.sh)
    
P.S: You need to have sudo access to your machine to run the above.

* You will be asked mysql root username and password while running the above.
Provide those.
* Basic authentication is used to do the REST operations. You can use `admin:asdf1234` as `username:password` pair.
* To add more users for doing the above operations, add the corresponding username and the **base64 encoded password** of the user in the `auth` table in the `wings` database.



Usage:
---

*  **Get all products:**
	* URL: `/products`
    * Method: `GET`
    * Query parameters: 
    	* `start`: The starting product id.
        * `size`: Number of products to retrieve
        * `includeAttributes`: If set to `true`, the attributes for the products will be included.
    * Example:
    	* Request: `GET /products?start=0&size=6&includeAttributes=true`
        * Response:
    ```json
{
    "data": [
        {
            "id": "35",
            "name": "nokia-z",
            "attributes": [
                {
                    "name": "category",
                    "value": "mobiles"
                }
            ]
        }
    ],
    "start": 0,
    "size": 1
}
```

        
*	**Get a single product:**
	* URL: `/products/{productName}`
    * Method: `GET`
    * Query parameters: 
        * `includeAttributes`: If set to `true`, the attributes for the products wi
    * Example:
    	* Request: `GET /products/nokia-z?includeAttributes=true`
        * Response:
    ```json
    {
    "id": "35",
    "name": "nokia-z",
    "attributes": [
        {
            "name": "category",
            "value": "mobiles"
        }
    ]
}
    ```
    If the product does not exits, `404 Not found` will be returned.
*    **Add a product:**
	* URL: `/products/`
    * Method: `POST`
    * Request Headers: 
        * `Content-type:application/json`
    * Request Body:
    ```json
    {
    "name": "moto-i",
    "attributes": [
        {
            "name": "category",
            "value": "mobiles"
        }
    ]
}
    ```
    * Response: Same as `/products/moto-i?includeAttributes=true`
      
      If the product already exists, `409 conflict` will be returned.
* **Delete a product:**      
	* URL: `/products/{productName}`
    * Method: `DELETE`
    * Response: `204 No Content` on successful deletion.
    
      If the product is not present, `404 Not found` will be returned.
* **Query for a product:**      
	* URL: `/query`
    * Method: `GET`
    * Query parameter: A single query parameter: attribute key and value. Apart from the key-value pair of an attribute, the parameters `includeAttributes`, `start` and `size can be given.` If multiple attributes are give, `400 Bad Request` will be thrown.
    
    Example: `/query?category=mobiles&start=1`
      
      

	

