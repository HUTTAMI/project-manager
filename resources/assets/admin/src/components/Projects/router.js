import Projects from './Projects.vue'
import AllProject from './AllProjects.vue'
import ActiveProject from './ActiveProjects.vue'

const UsersRoute = [
    {
        path: 'projects',
        component: Projects,
        children: [
            {
                path: '/',
                name: 'projects',
                component: ActiveProject

            },
            {
                path: '/all',
                name: 'all-projects',
                component: AllProject

            }
        ]
    }
]

export default UsersRoute
