<?php

namespace Database\Seeders;

use App\Models\Tutorial;
use Illuminate\Database\Seeder;

class TutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tutorials = [
            // JavaScript Tutorials
            [
                'language_id' => 'javascript',
                'title' => 'Introduction to JavaScript',
                'content' => 'JavaScript is a lightweight, interpreted programming language with first-class functions. It is most well-known as the scripting language for Web pages, but used in many non-browser environments as well.',
                'code_example' => 'console.log("Hello, World!");',
                'order' => 1,
                'is_published' => true,
            ],
            [
                'language_id' => 'javascript',
                'title' => 'Variables and Data Types',
                'content' => 'Learn about var, let, const and different data types in JavaScript. Variables are containers for storing data values. JavaScript has dynamic types, meaning the same variable can hold different types.',
                'code_example' => 'let name = "John";\nconst age = 25;\nvar isStudent = true;\nlet numbers = [1, 2, 3];',
                'order' => 2,
                'is_published' => true,
            ],
            [
                'language_id' => 'javascript',
                'title' => 'Functions',
                'content' => 'Functions are reusable blocks of code that perform specific tasks. They are one of the fundamental building blocks in JavaScript.',
                'code_example' => 'function greet(name) {\n  return `Hello, ${name}!`;\n}\n\nconst result = greet("World");\nconsole.log(result);',
                'order' => 3,
                'is_published' => true,
            ],
            [
                'language_id' => 'javascript',
                'title' => 'Arrays and Objects',
                'content' => 'Learn how to work with arrays and objects in JavaScript. Arrays are used to store multiple values in a single variable, while objects store collections of key-value pairs.',
                'code_example' => 'const arr = [1, 2, 3, 4, 5];\nconst obj = { \n  name: "Alice", \n  age: 30,\n  city: "New York"\n};\n\nconsole.log(arr[0]);\nconsole.log(obj.name);',
                'order' => 4,
                'is_published' => true,
            ],
            
            // Python Tutorials
            [
                'language_id' => 'python',
                'title' => 'Getting Started with Python',
                'content' => 'Python is a high-level, interpreted programming language known for its simplicity and readability. It is widely used in web development, data science, artificial intelligence, and more.',
                'code_example' => 'print("Hello, World!")',
                'order' => 1,
                'is_published' => true,
            ],
            [
                'language_id' => 'python',
                'title' => 'Variables and Data Types',
                'content' => 'Python has several built-in data types including integers, floats, strings, lists, tuples, and dictionaries. Variables in Python are dynamically typed.',
                'code_example' => 'name = "Alice"\nage = 25\nheight = 5.6\nis_student = True\ncolors = ["red", "green", "blue"]',
                'order' => 2,
                'is_published' => true,
            ],
            [
                'language_id' => 'python',
                'title' => 'Functions in Python',
                'content' => 'Functions in Python are defined using the def keyword. They help organize code into reusable blocks.',
                'code_example' => 'def greet(name):\n    return f"Hello, {name}!"\n\nresult = greet("World")\nprint(result)',
                'order' => 3,
                'is_published' => true,
            ],
            
            // HTML Tutorials
            [
                'language_id' => 'html',
                'title' => 'HTML Basics',
                'content' => 'HTML (HyperText Markup Language) is the standard markup language for creating web pages. It describes the structure of a web page using elements and tags.',
                'code_example' => '<!DOCTYPE html>\n<html>\n<head>\n  <title>My First Page</title>\n</head>\n<body>\n  <h1>Hello World!</h1>\n  <p>This is a paragraph.</p>\n</body>\n</html>',
                'order' => 1,
                'is_published' => true,
            ],
            [
                'language_id' => 'html',
                'title' => 'HTML Elements',
                'content' => 'HTML elements are the building blocks of web pages. Common elements include headings, paragraphs, links, images, and lists.',
                'code_example' => '<h1>Main Heading</h1>\n<p>This is a paragraph</p>\n<a href="https://example.com">Link</a>\n<img src="image.jpg" alt="Description">\n<ul>\n  <li>Item 1</li>\n  <li>Item 2</li>\n</ul>',
                'order' => 2,
                'is_published' => true,
            ],
            
            // CSS Tutorials
            [
                'language_id' => 'css',
                'title' => 'CSS Basics',
                'content' => 'CSS (Cascading Style Sheets) is used to style and layout web pages. It controls colors, fonts, spacing, and positioning of HTML elements.',
                'code_example' => 'body {\n  font-family: Arial, sans-serif;\n  background-color: #f0f0f0;\n}\n\nh1 {\n  color: #333;\n  text-align: center;\n}',
                'order' => 1,
                'is_published' => true,
            ],
            [
                'language_id' => 'css',
                'title' => 'Selectors and Properties',
                'content' => 'CSS selectors target HTML elements to apply styles. Properties define what styles to apply.',
                'code_example' => '.container {\n  max-width: 1200px;\n  margin: 0 auto;\n  padding: 20px;\n}\n\n#header {\n  background: #007bff;\n  color: white;\n}',
                'order' => 2,
                'is_published' => true,
            ],
            
            // React Tutorials
            [
                'language_id' => 'react',
                'title' => 'Introduction to React',
                'content' => 'React is a JavaScript library for building user interfaces. It lets you create reusable components and efficiently update the UI when data changes.',
                'code_example' => 'import React from \'react\';\n\nfunction Welcome() {\n  return <h1>Hello, React!</h1>;\n}\n\nexport default Welcome;',
                'order' => 1,
                'is_published' => true,
            ],
            [
                'language_id' => 'react',
                'title' => 'Components and Props',
                'content' => 'Components are the building blocks of React applications. Props allow you to pass data from parent to child components.',
                'code_example' => 'function Greeting(props) {\n  return <h1>Hello, {props.name}!</h1>;\n}\n\nfunction App() {\n  return <Greeting name="Alice" />;\n}',
                'order' => 2,
                'is_published' => true,
            ],
            
            // Node.js Tutorials
            [
                'language_id' => 'nodejs',
                'title' => 'Getting Started with Node.js',
                'content' => 'Node.js is a JavaScript runtime built on Chrome\'s V8 engine. It allows you to run JavaScript on the server side.',
                'code_example' => 'const http = require(\'http\');\n\nconst server = http.createServer((req, res) => {\n  res.writeHead(200, {\'Content-Type\': \'text/plain\'});\n  res.end(\'Hello World!\');\n});\n\nserver.listen(3000);',
                'order' => 1,
                'is_published' => true,
            ],
            [
                'language_id' => 'nodejs',
                'title' => 'Working with Modules',
                'content' => 'Node.js uses the CommonJS module system. You can create and import modules to organize your code.',
                'code_example' => '// math.js\nmodule.exports = {\n  add: (a, b) => a + b,\n  multiply: (a, b) => a * b\n};\n\n// app.js\nconst math = require(\'./math\');\nconsole.log(math.add(2, 3));',
                'order' => 2,
                'is_published' => true,
            ],
        ];

        foreach ($tutorials as $tutorial) {
            Tutorial::create($tutorial);
        }

        $this->command->info('Tutorial seeder completed successfully!');
    }
}
