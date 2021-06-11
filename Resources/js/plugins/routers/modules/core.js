export default [
    // 核心模块
    {
        path:"/Admin/goodcatch/m/core/index",name:"goodcatch_m_core_index",component:()=>import("@/views/goodcatch/modules/core/index"),children:[

            {path:"/Admin/goodcatch/m/core/index",name:"goodcatch_m_core_default",component:()=>import("@/views/goodcatch/modules/core/index")}, // 打开默认页面


            {path:"/Admin/goodcatch/m/core/areas",name:"goodcatch_m_core_admin_areas",component:()=>import("@/views/goodcatch/modules/core/admin/areas/index")},

            {path:"/Admin/goodcatch/m/core/form/:id?",name:"goodcatch_m_core_admin_areas_form",component:()=>import("@/views/goodcatch/modules/core/admin/areas/form")},
        ]
    }
];