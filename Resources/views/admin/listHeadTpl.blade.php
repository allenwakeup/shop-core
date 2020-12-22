@if (array_get ($data, 'status.templet') === '#statusText')
    <script type="text/html" id="statusText">
        <%# if(d.status === 1) { %>
        <span class="layui-badge layui-bg-green">启用</span>
        <%# } else { %>
        <span class="layui-badge layui-bg-red">禁用</span>
        <%# } %>
    </script>
@endif
@if (array_get ($data, 'status.templet') === '#statusSwitch')
    <script type="text/html" id="statusSwitch">
        <input type="checkbox" name="status" lay-skin="switch" lay-text="启用|禁用"
        <%# if (d.status == 1) { %>
        checked
        <%# } %>
        >
    </script>
@endif
@if (array_get ($data, 'enable.templet') === '#enableText')
    <script type="text/html" id="enableText">
        <%# if(d.enable === 1) { %>
        <span class="layui-badge layui-bg-green">启用</span>
        <%# } else { %>
        <span class="layui-badge layui-bg-red">禁用</span>
        <%# } %>
    </script>
@endif
@if (array_get ($data, 'gender.templet') === '#genderText')
    <script type="text/html" id="genderText">
        <%# if(d.gender === 1) { %>
        <span class="layui-badge layui-bg-green">男</span>
        <%# } else if(d.gender === 2) { %>
        <span class="layui-badge layui-bg-red">女</span>
        <%# } else { %>
        <span class="layui-badge layui-bg-gray">未知</span>
        <%# } %>
    </script>
@endif
@if (array_get ($data, 'attachment.templet') === '#attachmentText')
    <script type="text/html" id="attachmentText">
        <%# if(d.attachment) { %>
        <a class="layui-table-link" href="<% d.attachment.path %>"><% d.attachment.name %></a>
        <%# } else { %>

        <%# } %>
    </script>
@endif
@if (array_get ($data, 'pid.templet') === '#pidText')
    <script type="text/html" id="pidText">
        <%# if(d.parent) { %>
        <% d.parent.name %>
        <%# } else { %>
        --
        <%# } %>
    </script>
@endif
@if (array_get ($data, 'datasource.templet') === '#datasourceText')
    <script type="text/html" id="datasourceText">
        <%# if(d.datasource) { %>
        <% d.datasource.name %>
        <%# } else { %>
        --
        <%# } %>
    </script>
@endif
@if (array_get ($data, 'connection.templet') === '#connectionText')
    <script type="text/html" id="connectionText">
        <%# if(d.connection) { %>
        <%# if(d.connection.datasource) { %>
        「<% d.connection.datasource.name %>」
        <%# } %>
        <% d.connection.name %>
        <%# } else { %>
        --
        <%# } %>
    </script>
@endif