export default [
    {
        _name: 'CSidebarNav',
        _children: [
            {
                _name: 'CSidebarNavItem',
                name: 'Dashboard',
                to: '/',
                icon: 'cil-bar-chart'
            },
            {
                _name: 'CSidebarNavTitle',
                _children: ['Management']
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Employees',
                to: '/employees',
                icon: 'cil-address-book'
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Requests individual',
                to: '/requests',
                icon: 'cil-notes'
            },

            {
                _name: 'CSidebarNavDropdown',
                name: 'Organization',
                route: '/buttons',
                icon: 'cil-sitemap',
                items: [
                    {
                        name: 'Position',
                        to: '/buttons/standard-buttons'
                    },
                    {
                        name: 'Department',
                        to: '/buttons/dropdowns'
                    }
                ]
            },
            {
                _name: 'CSidebarNavDropdown',
                name: 'Projects',
                route: '/projects',
                icon: 'cil-briefcase',
                items: [
                    {
                        name: 'List',
                        to: '/buttons/standard-buttons'
                    }
                ]
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Workspace',
                route: '/workspaces',
                icon: 'cil-3d'
            },
            {
                _name: 'CSidebarNavItem',
                name: 'Devices',
                route: '/devices',
                icon: 'cil-monitor'
            },
        ]
    }
];
