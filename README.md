### Usage

1. Add the file `dynamic-acf-icon.php` to the folder **elements** in your **Bricks Child Theme**.

2. Inside your `function.php` file, add the following code:

   ```php
   /**
    * Register custom elements
    */
   add_action( 'init', function() {
     $element_files = [
   	__DIR__ . '/elements/dynamic-acf-icon.php',
     ];
   
     foreach ( $element_files as $file ) {
       \Bricks\Elements::register_element( $file );
     }
   }, 11 );
   ```

3. Create a new ACF field **Image** (e.g. *Featured Icon*)

4. Set the **Return Format** to **Image Array**.

5. Open Bricks Builder and add the new element **Dynamic ACF Icon**.

6. In the element settings, select your ACF field name (e.g. *featured_icon*) as the dynamic data field

7. Style the icon