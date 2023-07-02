# webservco/component

A PHP component/library.

---

Project skeleton / project started.

## Usage

- [Create project repository](https://github.com/organizations/webservco/repositories/new);

### Customize

```shell
git clone git@github.com:webservco/component.git {component}
cd {component}
git remote set-url origin git@github.com:webservco/{component}.git
rm -f src/WebServCo/.gitignore && git add src/WebServCo && git commit -m 'Init src'
vim README.md # (name, description)
vim composer.json # (name)
git add README.md && git add composer.json && git commit -m 'Customize' && git push -u origin main
```

---

## Index

- webservco/command
- webservco/component (project template)
- webservco/configuration
- webservco/configuration-legacy
- webservco/controller
- webservco/database
- webservco/database-legacy
- webservco/data-transfer
- webservco/log
- webservco/view
