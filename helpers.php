<?php 

/**
 * get the base path
 * 
 * @param  string $path
 * @return string
 */

 function basePath($path = '') 
 {
    return __DIR__ . '/' . $path;
 }

/**
 * Load a view
 * @param string $name
 * @return void
 */
function loadView($name, $data = [])
{
   $viewPath = basePath("App/views/{$name}.view.php");
   extract($data);
   if(file_exists($viewPath)) {
      require_once $viewPath;
   } else {
      echo "View {$name} not found";
   }
}

/**
 * Load a partials
 * @param string $name
 * @return void
 */
function loadPartials($name, $data = [])
{
   $partialPath = basePath("App/views/partials/{$name}.php");
   if(file_exists($partialPath)) {
      extract($data);
      require_once $partialPath;
   } else {
      echo "Partial {$name} not found";
   }
}

/**
 * Inspect a values(s)
 * 
 * @param mixed $values
 * @return void
 */
function dd($values)
{
   echo "<pre>";
      var_dump($values);
   echo "</pre>";
}

/**
 * Inspect a values(s) and die
 * 
 * @param mixed $values
 * @return void
 */
function ddAndDie($values)
{
   echo "<pre>";
      die(var_dump($values));
   echo "</pre>";
}

/**
 * Format salary
 * 
 * @param string $salary
 * @return string Formatted Salary
 */
function formattedSalary($salary)
{
   return '$' . number_format(floatval($salary));
}

/**
 * Sanitize data
 * 
 * @param string $dirty
 * @return string
 */
function sanitize($dirty)
{
   $dirty = filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
   $dirty = htmlspecialchars($dirty);
   $dirty = stripslashes($dirty);
   return $dirty;
}

/**
 * Redired to a given url
 * 
 * @param string $url
 * @return void
 */
function redirec($url)
{
   header("Location: {$url}");
   exit;
}