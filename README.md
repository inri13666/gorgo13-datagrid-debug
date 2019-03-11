#### Datagrid Debugger

```bash
php bin/console gorgo:debug:datagrid organization-view-users-grid
```
or
```bash
php app/console gorgo:debug:datagrid organization-view-users-grid
```

Result:

| Datagrid Name                | Type | Parent             |
|------------------------------|------|--------------------|
| organization-view-users-grid | orm  | user-relation-grid |

```yml
datagrids:
    organization-view-users-grid:
        source:
            type: orm
            query:
                select:
                    - u.id
                    - u.username
                    - u.email
                    - u.firstName
                    - u.lastName
                    - u.enabled
                from:
                    -
                        table: 'OroUserBundle:User'
                        alias: u
                where:
                    and:
                        - ':organization_id MEMBER OF u.organizations'
            bind_parameters:
                - organization_id
        columns:
            firstName:
                label: oro.user.first_name.label
            lastName:
                label: oro.user.last_name.label
            email:
                label: oro.user.email.label
            username:
                label: oro.user.username.label
            enabled:
                label: oro.user.enabled.label
                frontend_type: select
                choices:
                    - Disabled
                    - Enabled
        properties:
            id: null
        sorters:
            columns:
                username:
                    data_name: u.username
                email:
                    data_name: u.email
                firstName:
                    data_name: u.firstName
                lastName:
                    data_name: u.lastName
            disable_default_sorting: true
            default:
                lastName: ASC
        filters:
            columns:
                firstName:
                    type: string
                    data_name: u.firstName
                lastName:
                    type: string
                    data_name: u.lastName
                email:
                    type: string
                    data_name: u.email
                username:
                    type: string
                    data_name: u.username
                enabled:
                    type: boolean
                    data_name: u.enabled
                    options:
                        field_options: { choices: { 2: Disabled, 1: Enabled } }
        name: organization-view-users-grid
        acl_resource: oro_organization_view
        extends: user-relation-grid
```

#### Datagrid Profiler

```bash
php bin/console gorgo:profile:datagrid organization-view-users-grid --current-user=admin --current-organization=1 --bind={\"organization_id\":1}
```
or
```bash
php app/console gorgo:profile:datagrid organization-view-users-grid --current-user=admin --current-organization=1 --bind={\"organization_id\":1}
```

Result:

| Organization | First name | Last name | Primary Email     | Username | Enabled |  Tags   |
|--------------|------------|-----------|-------------------|----------|---------|---------|
| OroCRM       | John       | Doe       | admin@example.com | admin    | 1       |         |
