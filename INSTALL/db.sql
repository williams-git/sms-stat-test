CREATE DATABASE smsinfo
  WITH OWNER = postgres
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'en_US.UTF-8'
       LC_CTYPE = 'en_US.UTF-8'
       CONNECTION LIMIT = -1;

-----------------------------------------------------------------------------------------

CREATE TABLE public.countries
(
  cnt_id serial,
  cnt_code integer NOT NULL,
  cnt_title character varying(64) NOT NULL,
  cnt_created timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT countries_pkey PRIMARY KEY (cnt_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.countries
  OWNER TO postgres;

CREATE INDEX countries_cnt_code_idx
  ON public.countries
  USING btree
  (cnt_code);

-----------------------------------------------------------------------------------------

CREATE TABLE public.users
(
  usr_id serial,
  usr_name character varying(128),
  usr_active boolean NOT NULL DEFAULT false,
  usr_created timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT users_pkey PRIMARY KEY (usr_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.users
  OWNER TO postgres;

CREATE INDEX users_usr_active_idx
  ON public.users
  USING btree
  (usr_active);

-----------------------------------------------------------------------------------------

CREATE TABLE public.numbers
(
  num_id serial,
  cnt_id integer NOT NULL,
  num_number integer NOT NULL,
  num_created timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT numbers_pkey PRIMARY KEY (num_id),
  CONSTRAINT numbers_cnt_id_fkey FOREIGN KEY (cnt_id)
      REFERENCES public.countries (cnt_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY DEFERRED,
  CONSTRAINT numbers_cnt_id_num_number_key UNIQUE (cnt_id, num_number)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.numbers
  OWNER TO postgres;

-----------------------------------------------------------------------------------------

CREATE TABLE public.send_log
(
  log_id bigserial,
  usr_id integer NOT NULL,
  num_id integer NOT NULL,
  log_success boolean NOT NULL DEFAULT false,
  log_created timestamp without time zone NOT NULL DEFAULT now(),
  log_message text,
  CONSTRAINT send_log_pkey PRIMARY KEY (log_id),
  CONSTRAINT send_log_num_id_fkey FOREIGN KEY (num_id)
      REFERENCES public.numbers (num_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY DEFERRED,
  CONSTRAINT send_log_usr_id_fkey FOREIGN KEY (usr_id)
      REFERENCES public.users (usr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY DEFERRED
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.send_log
  OWNER TO postgres;

CREATE INDEX fki_send_log_num_id_fkey
  ON public.send_log
  USING btree
  (num_id);

CREATE INDEX fki_send_log_usr_id_fkey
  ON public.send_log
  USING btree
  (usr_id);

-----------------------------------------------------------------------------------------

CREATE TABLE public.send_log_aggregated
(
  lga_date date NOT NULL,
  cnt_id integer NOT NULL,
  usr_id integer NOT NULL,
  lga_sent bigint NOT NULL DEFAULT 0,
  lga_failed bigint NOT NULL DEFAULT 0,
  CONSTRAINT send_log_aggregated_pkey PRIMARY KEY (lga_date, cnt_id, usr_id),
  CONSTRAINT send_log_aggregated_cnt_id_fkey FOREIGN KEY (cnt_id)
      REFERENCES public.countries (cnt_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY DEFERRED,
  CONSTRAINT send_log_aggregated_usr_id_fkey FOREIGN KEY (usr_id)
      REFERENCES public.users (usr_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY DEFERRED
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.send_log_aggregated
  OWNER TO postgres;

CREATE INDEX fki_send_log_aggregated_cnt_id_fkey
  ON public.send_log_aggregated
  USING btree
  (cnt_id);

CREATE INDEX fki_send_log_aggregated_usr_id_fkey
  ON public.send_log_aggregated
  USING btree
  (usr_id);








