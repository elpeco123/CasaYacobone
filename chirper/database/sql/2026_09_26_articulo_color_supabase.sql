-- ============================================================================
--  Casa Yacobone · Artículo y color en productos (se elimina marca)
--  Equivalente en SQL de la migración 2026_09_26_000001.
--  Pegar en el SQL Editor de Supabase y ejecutar una sola vez.
--  Correrlo de nuevo no rompe nada.
--
--  OJO: borra la columna "marca" y lo que tenga cargado. Si querés guardarte
--  esos valores antes, corré primero:
--    select id, nombre, marca from public.productos order by id;
-- ============================================================================

begin;

-- 1. Columnas nuevas ------------------------------------------------------
-- articulo: los productos con el mismo código son el mismo modelo en distinto
-- talle o color. color: la otra variante, además del talle.
alter table public.productos
    add column if not exists articulo varchar(255) null,
    add column if not exists color    varchar(255) null;

-- 2. Lo ya cargado arranca con su propio nombre como artículo -------------
update public.productos set articulo = nombre where articulo is null;

-- 3. El artículo pasa a ser obligatorio -----------------------------------
alter table public.productos alter column articulo set not null;

create index if not exists productos_articulo_index on public.productos (articulo);

-- 4. Se va la marca -------------------------------------------------------
alter table public.productos drop column if exists marca;

-- 5. Dejar la migración marcada como aplicada -----------------------------
-- Solo si esta base usa la tabla "migrations" de Laravel.
do $$
begin
    if to_regclass('public.migrations') is not null then
        insert into public.migrations (migration, batch)
        select '2026_09_26_000001_replace_marca_with_articulo_on_productos_table',
               (select coalesce(max(batch), 0) + 1 from public.migrations)
        where not exists (
            select 1 from public.migrations
            where migration = '2026_09_26_000001_replace_marca_with_articulo_on_productos_table'
        );
    end if;
end $$;

commit;

-- Control: las bombachas que sean el mismo modelo tienen que quedar con el
-- mismo código. Después de correr esto, entrá a Productos y unificá a mano los
-- que correspondan (por ejemplo, poner BC-100 en todos los talles del modelo).
-- select articulo, nombre, talle, color, precio_venta, stock
-- from public.productos order by articulo, talle;
