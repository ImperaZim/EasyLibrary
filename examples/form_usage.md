## [libform](https://github.com/ImperaZim/EasyLibrary/blob/development/src/internal/libform)
Naturally you can use my libform like formAPI where you create an instance of the FormType you want, but aiming for code structure I decided to create an extension of libform where you create a class that extends the interface/Form and defines the values within the class with some predefined functions.
__See below for more on how to use__ **Form**

## [interface/Form](https://github.com/ImperaZim/EasyLibrary/blob/development/src/library/interface/Form.php)
The Form class allows you to create forms separated by classes where you can create a class that extends the Form ``final class FormExample extends Form {...}`` and within this class you must define the form settings of the method `` structure(): Form;``, see below how to use the Form class and each of the available FormTypes.

## Plugin Example 
See how the structure should work in a plugin [ExampleForm](https://github.com/ImperaZim/EasyLibrary/blob/development/examples/PluginExample/src/ImperaZim/forms/ExampleForm.php)

## Usage [FormTypes](https://github.com/ImperaZim/EasyLibrary/blob/development/src/internal/libform/types)

### LongForm 
See step by step how to create a form class in [LongForm Usage](long_form_usage.md).