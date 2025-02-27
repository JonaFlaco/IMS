##Create New Doc


####Getting Started
---------------
To create new doc, click [here](/documentations) then click `Add Documentations`. then you will get a form with below fields
- `Name`: Name of the doc without speical chars and whitespace.
- `Title`: Title of the doc.
- `Description`: Description of the doc.
- `Menu`: Select a menu to appear at left side of screen which replace the main menu of the system while the user reading the doc, to help the user navigate through the doc easily, If you don't have you can create a new menu [here](/menu)

From database side that is it, then we need to open `\src\docs` and create a folder with same name as you wrote for the doc, If you named the doc `developers_doc` you need to name the folder same thing and the path will be `src\docs\developers_doc`. Then you can create files and folder in that folder. Some points to keep in mind while writing docs:
- There is a limitation for strucutring your folders in which you can have only one folder inside your docs folder. means You will have root folder and one sub folder. If you go deeper system will not recognize the files it will only read root and folders in root.
- You should write docs in MarkDown files which the extension is `.md`.
- Folder and file names should not contain whitespace.


####Example
---------------
In this example I will show you how to create a new doc and add some files to it. If you go to [here](/documentations) and create a new doc with below info:
- `Name`: `developers`
- `Title`: `Develoers Documentation`.
- `Description`: `This documentation is for developers`.
- `Menu`: `Leave it empty` 

Then go to `src\docs` and create a new folder with same name as the doc you just created which is `developers`. Now create your first doc file inside `developers` folder and call it `index.md` which the full path of this file will be `src\docs\developers\index.md`.
Inside the file write below code and save it.

```
# Hello IMS
```

Then in browser go to this link `/{doc name}/index` which in our case will be `/developers/index` you should see result of your doc there.