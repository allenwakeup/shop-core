import common from '../common';
const baseUrl = common.baseUrl;

export default {

    /**
     * 模块化接口
     */

    "moduleCoreAreas": baseUrl + "Admin/goodcatch/m/core/areas", // 地区
    "moduleCoreDatasources": baseUrl + "Admin/goodcatch/m/core/datasources", // 数据源
    "moduleCoreDatabases": baseUrl + "Admin/goodcatch/m/core/databases", // 数据库信息
    "moduleCoreConnections": baseUrl + "Admin/goodcatch/m/core/connections", // 连接
    "moduleCoreTestConnection": baseUrl + "Admin/goodcatch/m/core/connections/test", // 测试连接
    "moduleCoreSchedules": baseUrl + "Admin/goodcatch/m/core/schedules", // 计划与任务
};
