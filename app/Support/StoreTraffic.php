<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StoreTraffic
{
    public static function recordRequest(Request $request): void
    {
        if (! Schema::hasTable('store_traffic_daily')) {
            return;
        }

        if ($request->is('admin', 'admin/*', 'livewire/*', '_deploy/*', 'feeds/*')) {
            return;
        }

        if ($request->ajax() && ! $request->pjax()) {
            return;
        }

        $date = now()->toDateString();
        $sessionKey = 'beautage_traffic_logged_'.$date;
        $newSession = ! $request->session()->get($sessionKey, false);

        if ($newSession) {
            $request->session()->put($sessionKey, true);
        }

        $row = DB::table('store_traffic_daily')->where('date', $date)->first();

        if ($row === null) {
            DB::table('store_traffic_daily')->insert([
                'date' => $date,
                'sessions' => 1,
                'page_views' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            static::touchLiveVisitor($request);

            return;
        }

        DB::table('store_traffic_daily')->where('date', $date)->update([
            'sessions' => (int) $row->sessions + ($newSession ? 1 : 0),
            'page_views' => (int) $row->page_views + 1,
            'updated_at' => now(),
        ]);

        static::touchLiveVisitor($request);
    }

    public static function touchLiveVisitor(Request $request): void
    {
        if (! Schema::hasTable('store_live_presence')) {
            return;
        }

        $sessionId = $request->session()->getId();
        if ($sessionId === '') {
            return;
        }

        $now = now();

        DB::table('store_live_presence')->updateOrInsert(
            ['session_id' => $sessionId],
            ['last_seen_at' => $now, 'updated_at' => $now],
        );

        DB::table('store_live_presence')
            ->where('last_seen_at', '<', $now->copy()->subMinutes(5))
            ->delete();
    }

    public static function liveVisitorCount(): int
    {
        if (! Schema::hasTable('store_live_presence')) {
            return 0;
        }

        return (int) DB::table('store_live_presence')
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->count();
    }
}
