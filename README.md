# webservco/component

A PHP component/library.

---

Project skeleton / project starter.

## Workflow

### [Create project repository](https://github.com/organizations/webservco/repositories/new);

### Set up project name

```sh
COMPONENT_NAME='COMPONENT_NAME'
```

### Run customization commands

```sh
cd ~/p/webservco && \
git clone git@github.com:webservco/component.git $COMPONENT_NAME && \
cd $COMPONENT_NAME && \
git remote set-url origin git@github.com:webservco/$COMPONENT_NAME.git && \
rm -f src/WebServCo/.gitignore && git add src/WebServCo && git commit -m 'Init src' && \
printf '%s\n' "# webservco/$COMPONENT_NAME" '' 'A PHP component/library.' '' '---' > README.md && \
sed -i -e "s|\"name\" : \"webservco/component\"|\"name\" : \"webservco/$COMPONENT_NAME\"|g" composer.json && \
git add README.md && \
git add composer.json && \
git commit -m 'Customize' && \
git push -u origin main && \
mkdir bin config public resources tests && \
cd ~/p/webservco
```

### Add code

### Publish tag

### [Publish on packagist](https://packagist.org/packages/submit)

---

## Index

- webservco/application
- webservco/command
- webservco/component (project template)
- webservco/configuration
- webservco/configuration-legacy
- webservco/controller
- webservco/data
- webservco/database
- webservco/database-legacy
- webservco/data-transfer
- webservco/dependency-container
- webservco/document-object-model
- webservco/emitter
- webservco/environment
- webservco/error
- webservco/exception
- webservco/http
- webservco/log
- webservco/middleware
- webservco/route
- webservco/stopwatch
- webservco/view
