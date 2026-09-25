-- ============================================================================
--  Casa Yacobone · Cuentas corrientes, débito/crédito y recargo
--  Equivalente en SQL de las migraciones 2026_09_25_000001 a 000004.
--  Pegar tal cual en el SQL Editor de Supabase y ejecutar una sola vez.
--  Es seguro correrlo de nuevo: no duplica nada.
--
--  Si algo falla, no se aplica nada: todo va dentro de una transacción.
--  Chequeo previo opcional, para confirmar que las tablas están en "public":
--    select table_name from information_schema.tables
--    where table_schema = 'public' and table_name in ('ventas','cajas','users');
-- ============================================================================

begin;

-- 1. Clientes de cuenta corriente ------------------------------------------
create table if not exists public.clientes (
    id          bigserial primary key,
    nombre      varchar(255) not null,
    apellido    varchar(255) not null,
    telefono    varchar(255) not null unique,
    direccion   varchar(255) not null,
    created_at  timestamp(0) without time zone null,
    updated_at  timestamp(0) without time zone null
);

-- 2. Ventas: cliente de la cuenta corriente y recargo del crédito ----------
alter table public.ventas
    add column if not exists cliente_id     bigint null,
    add column if not exists monto_recargo  numeric(12, 2) not null default 0;

do $$
begin
    if not exists (
        select 1 from pg_constraint where conname = 'ventas_cliente_id_foreign'
    ) then
        alter table public.ventas
            add constraint ventas_cliente_id_foreign
            foreign key (cliente_id) references public.clientes (id) on delete set null;
    end if;
end $$;

create index if not exists ventas_cliente_id_index on public.ventas (cliente_id);

-- 3. Cobros de cuenta corriente -------------------------------------------
create table if not exists public.cuenta_corriente_pagos (
    id          bigserial primary key,
    cliente_id  bigint not null references public.clientes (id) on delete cascade,
    user_id     bigint not null references public.users (id),
    monto       numeric(12, 2) not null,
    tipo_pago   varchar(255) not null default 'efectivo',
    nota        varchar(255) null,
    created_at  timestamp(0) without time zone null,
    updated_at  timestamp(0) without time zone null
);

create index if not exists cuenta_corriente_pagos_cliente_id_index
    on public.cuenta_corriente_pagos (cliente_id);

-- 4. Cajas: totales de débito, crédito y cuenta corriente ------------------
-- total_tarjeta queda como está, para las cajas viejas.
alter table public.cajas
    add column if not exists total_debito            numeric(12, 2) not null default 0,
    add column if not exists total_credito           numeric(12, 2) not null default 0,
    add column if not exists total_cuenta_corriente  numeric(12, 2) not null default 0;

-- 5. Dejar las migraciones marcadas como aplicadas -------------------------
-- Solo si esta base usa la tabla "migrations" de Laravel. Si no existe, no
-- hace falta: significa que el esquema no se maneja con `php artisan migrate`.
do $$
begin
    if to_regclass('public.migrations') is not null then
        insert into public.migrations (migration, batch)
        select t.m, (select coalesce(max(batch), 0) + 1 from public.migrations)
        from (values
            ('2026_09_25_000001_create_clientes_table'),
            ('2026_09_25_000002_add_cliente_y_recargo_to_ventas_table'),
            ('2026_09_25_000003_create_cuenta_corriente_pagos_table'),
            ('2026_09_25_000004_add_totales_pago_to_cajas_table')
        ) as t(m)
        where not exists (
            select 1 from public.migrations where migration = t.m
        );
    end if;
end $$;

commit;

-- Control rápido: las tres consultas de abajo tienen que devolver datos.
-- select column_name from information_schema.columns where table_name = 'ventas' and column_name in ('cliente_id','monto_recargo');
-- select column_name from information_schema.columns where table_name = 'cajas' and column_name like 'total_%';
-- select migration, batch from public.migrations order by id desc limit 4;
