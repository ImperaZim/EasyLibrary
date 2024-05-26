## [libform](https://github.com/ImperaZim/EasyLibrary/blob/development/src/internal/libform)
naturally you can use my libform like formAPI where you create an instance of the FormType you want, but aiming for code structure I decided to create an extension of libform where you create a class that extends the interface/Form and defines the values within the class with some predefined functions.  See below for more on how to use **Form**

## interface/Form 
This class allows you to create forms much faster than using the pure libform because it allows you to create a form in a class and send a played time just by calling that class with the player parameter in the constructor.

## Usage 

### Plugin Example 
See how the structure should work in a plugin [ExampleForm](https://github.com/ImperaZim/EasyLibrary/blob/development/examples/PluginExample/src/ImperaZim/forms/ExampleForm.php)

### LongForm 
See step by step how to create a form class in [LongForm Usage](long_form_usage.md).