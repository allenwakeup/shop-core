@include('admin.listHeadTpl')
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
@if (array_get ($data, 'product.templet') === '#productText')
<script type="text/html" id="productText">
    <%# if(d.product) { %>
        <% d.product.name %>
        <% d.product.type %>
    <%# } else { %>
        --
    <%# } %>
</script>
@endif