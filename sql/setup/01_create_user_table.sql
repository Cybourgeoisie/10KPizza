CREATE TABLE "user" (
	"user_id"           BIGSERIAL PRIMARY KEY,
	"uuid"              UUID NOT NULL DEFAULT gen_random_uuid(),
	"email"             VARCHAR(256) UNIQUE NOT NULL,
	"original_email"    VARCHAR(256) UNIQUE NOT NULL,
	"password"          VARCHAR(256) NOT NULL,
	"portfolio"         TEXT,
	"settings"          TEXT,
	"session"           VARCHAR(128),
	"verification_code" VARCHAR(128),
	"verified"          BOOLEAN NOT NULL DEFAULT false,
	"status"            BOOLEAN DEFAULT true,
	"created"           TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
	"last_active"       TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);