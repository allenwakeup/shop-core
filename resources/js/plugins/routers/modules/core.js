export default [
    // 核心模块
    {
        path:"/Admin/goodcatch/m/core/index",name:"goodcatch_m_core_index",component:()=>import("@/views/Admin/index"),children:[
            {path:"/Admin/goodcatch/m/core/index",name:"goodcatch_m_core_default",component:()=>import("@/views/Admin/default")}, // 打开默认页面

            // 地区
            {path:"/Admin/goodcatch/m/core/areas",name:"goodcatch_m_core_admin_areas",component:()=>import("@/views/goodcatch/modules/core/admin/areas/index")},
            {path:"/Admin/goodcatch/m/core/areas/form/:id?",name:"goodcatch_m_core_admin_areas_form",component:()=>import("@/views/goodcatch/modules/core/admin/areas/form")},

            // 数据源
            {path:"/Admin/goodcatch/m/core/datasources",name:"goodcatch_m_core_admin_datasources",component:()=>import("@/views/goodcatch/modules/core/admin/datasources/index")},
            {path:"/Admin/goodcatch/m/core/datasources/form/:id?",name:"goodcatch_m_core_admin_datasources_form",component:()=>import("@/views/goodcatch/modules/core/admin/datasources/form")},

            // 数据库信息
            {path:"/Admin/goodcatch/m/core/databases",name:"goodcatch_m_core_admin_databases",component:()=>import("@/views/goodcatch/modules/core/admin/databases/index")},


            // 连接
            {path:"/Admin/goodcatch/m/core/connections",name:"goodcatch_m_core_admin_connections",component:()=>import("@/views/goodcatch/modules/core/admin/connections/index")},
        ]
    }
];
