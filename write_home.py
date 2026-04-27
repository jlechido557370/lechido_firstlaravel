#!/usr/bin/env python3
"""Reconstruct home.blade.php with all enhancements"""

content = r'''@extends('layouts.app')
@section('title', 'Home \u2014 dotLibrary')

@section('content')

{{-- \u2500\u2500 HERO BANNER \u2500\u2500 --}}
<div class="hero-glass" style="
    border-radius: 20px;
    padding: 52px 48px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(148,163,184,.18);
    animation: heroFadeIn .6s ease both;
">
    {{-- Decorative rings --}}
    <div style="position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;border:1px solid rgba(255,255,255,.04);pointer-events:none;"></div>
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;border:1px solid rgba(255,255,255,.06);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;left:200px;width:200px;height:200px;border-radius:50%;border:1px solid rgba(255,255,255,.03);pointer-events:none;"></div>

    <div style="position:relative;z-index:1;max-width:680px;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);padding:6px 14px;border-radius:999px;margin-bottom:20px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;box-shadow:0 0 6px #4ade80;animation:pulse 2s infinite;"></span>
            <span style="font-size:11px;font-family:var(--font-mono);letter-spacing:.08em;color:rgba(255,255,255,.72);text-transform:uppercase;">Open \u00b7 Davao City</span>
        </div>

        <h1 style="font-family:var(--font-disp);font-size:clamp(42px,6.5vw,72px);color:#ffffff;line-height:.95;letter-spacing:.01em;margin-bottom:18px;font-weight:400;">
            .Library
        </h1>
        <p style="font-size:16px;color:rgba(255,255,255,.72);line-height:1.75;margin-bottom:28px;max-width:520px;">
            A modern digital library for Davao City. Browse thousands of titles, borrow books instantly, and manage your reading life \u2014 all in one place.
        </p>

        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
            <a href="{{ route('books.catalogue') }}" style="display:inline-flex;align-items:center;gap:8px;background:#ffffff;color:#0f172a;font-size:13.5px;font-weight:600;padding:11px 22px;border-radius:8px;text-decoration:none;transition:opacity .15s;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Browse Catalogue
            </a>
            @guest
            <a href="{{ route('register') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.9);font-size:13.5px;padding:11px 22px;border-radius:8px;border:1px solid rgba(255,255,255,.18);text-decoration:none;transition:opacity .15s;">
                Create Free Account
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            @endguest
            @auth
            <a href="{{ route('user.dashboard') }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.9);font-size:13.5px;padding:11px 22px;border-radius:8px;border:1px solid rgba(255,255,255,.18);text-decoration:none;transition:opacity .15s;">
                My Dashboard
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            @endauth
        </div>
    </div>

    {{--
