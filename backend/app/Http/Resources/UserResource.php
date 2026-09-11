<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Whitelist explicita dos dados do usuario expostos ao cliente.
     * Nada alem destes campos deve sair — evita vazar colunas internas.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'level' => $this->level,
            'xp_progress' => $this->xp_progress,
            'xp_levelup' => $this->xp_levelup,
            'is_admin' => (bool) $this->is_admin,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
