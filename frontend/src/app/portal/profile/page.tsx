"use client";

/* Avatar and gallery URLs come from the API (dynamic hosts); use native img for previews and remote storage. */
/* eslint-disable @next/next/no-img-element */

import Link from "next/link";
import { useCallback, useEffect, useRef, useState } from "react";
import {
  getStoredPortalToken,
  PortalApiError,
  portalAvatarRemove,
  portalAvatarUpload,
  portalPasswordUpdate,
  portalPhotoDelete,
  portalPhotoUpload,
  portalProfileGet,
  portalProfileUpdate,
  type PortalProfilePhoto,
  type PortalProfileResponse,
} from "@/lib/portal-api";

export default function PortalProfilePage() {
  const [token, setToken] = useState<string | null>(null);
  const [profile, setProfile] = useState<PortalProfileResponse["data"] | null>(
    null
  );
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [bio, setBio] = useState("");
  const [designation, setDesignation] = useState("");
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
  const avatarObjectUrl = useRef<string | null>(null);
  const avatarFileInputRef = useRef<HTMLInputElement>(null);
  const photoFileInputRef = useRef<HTMLInputElement>(null);

  const [loadError, setLoadError] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);
  const [profileMessage, setProfileMessage] = useState<string | null>(null);
  const [profileErr, setProfileErr] = useState<string | null>(null);
  const [passwordMessage, setPasswordMessage] = useState<string | null>(null);
  const [passwordErr, setPasswordErr] = useState<string | null>(null);
  const [avatarErr, setAvatarErr] = useState<string | null>(null);
  const [photosErr, setPhotosErr] = useState<string | null>(null);

  const applyProfile = useCallback((data: PortalProfileResponse["data"]) => {
    setProfile(data);
    setName(data.user.name);
    setEmail(data.user.email);
    setPhone(data.user.phone ?? "");
    setBio(data.user.bio ?? "");
    setDesignation(data.user.designation ?? "");
  }, []);

  const load = useCallback(async (t: string) => {
    setBusy(true);
    setLoadError(null);
    try {
      const res = await portalProfileGet(t);
      applyProfile(res.data);
    } catch (e) {
      setLoadError(e instanceof Error ? e.message : "Could not load profile");
      setProfile(null);
    } finally {
      setBusy(false);
    }
  }, [applyProfile]);

  useEffect(() => {
    const t = getStoredPortalToken();
    setToken(t);
    if (t) {
      void load(t);
    }
  }, [load]);

  useEffect(() => {
    return () => {
      if (avatarObjectUrl.current) {
        URL.revokeObjectURL(avatarObjectUrl.current);
      }
    };
  }, []);

  function onAvatarFileChange(f: File | null) {
    if (avatarObjectUrl.current) {
      URL.revokeObjectURL(avatarObjectUrl.current);
      avatarObjectUrl.current = null;
    }
    if (!f) {
      setAvatarPreview(null);
      return;
    }
    const url = URL.createObjectURL(f);
    avatarObjectUrl.current = url;
    setAvatarPreview(url);
  }

  async function onSaveProfile(e: React.FormEvent) {
    e.preventDefault();
    if (!token) {
      return;
    }
    setBusy(true);
    setProfileErr(null);
    setProfileMessage(null);
    try {
      const res = await portalProfileUpdate(token, {
        name,
        email,
        phone: phone || "",
        bio: bio || "",
        designation: designation || "",
      });
      applyProfile(res.data);
      setProfileMessage("Profile saved.");
    } catch (err) {
      setProfileErr(
        err instanceof PortalApiError
          ? err.message
          : "Could not update profile"
      );
    } finally {
      setBusy(false);
    }
  }

  async function onSavePassword(e: React.FormEvent) {
    e.preventDefault();
    if (!token) {
      return;
    }
    setBusy(true);
    setPasswordErr(null);
    setPasswordMessage(null);
    try {
      await portalPasswordUpdate(token, {
        current_password: currentPassword,
        password: newPassword,
        password_confirmation: confirmPassword,
      });
      setCurrentPassword("");
      setNewPassword("");
      setConfirmPassword("");
      setPasswordMessage("Password updated.");
    } catch (err) {
      setPasswordErr(
        err instanceof PortalApiError ? err.message : "Could not update password"
      );
    } finally {
      setBusy(false);
    }
  }

  async function onUploadAvatar(file: File) {
    if (!token) {
      return;
    }
    setBusy(true);
    setAvatarErr(null);
    try {
      const res = await portalAvatarUpload(token, file);
      applyProfile(res.data);
      onAvatarFileChange(null);
    } catch (err) {
      setAvatarErr(
        err instanceof PortalApiError ? err.message : "Avatar upload failed"
      );
    } finally {
      setBusy(false);
    }
  }

  async function onRemoveAvatar() {
    if (!token) {
      return;
    }
    setBusy(true);
    setAvatarErr(null);
    try {
      const res = await portalAvatarRemove(token);
      applyProfile(res.data);
      onAvatarFileChange(null);
    } catch (err) {
      setAvatarErr(
        err instanceof PortalApiError ? err.message : "Could not remove avatar"
      );
    } finally {
      setBusy(false);
    }
  }

  async function onUploadPhoto(file: File) {
    if (!token) {
      return;
    }
    setBusy(true);
    setPhotosErr(null);
    try {
      const res = await portalPhotoUpload(token, file);
      setProfile((prev) =>
        prev
          ? { ...prev, photos: [...prev.photos, res.data.photo] }
          : prev
      );
    } catch (err) {
      setPhotosErr(
        err instanceof PortalApiError ? err.message : "Photo upload failed"
      );
    } finally {
      setBusy(false);
    }
  }

  async function onDeletePhoto(id: number) {
    if (!token) {
      return;
    }
    setBusy(true);
    setPhotosErr(null);
    try {
      await portalPhotoDelete(token, id);
      setProfile((prev) =>
        prev
          ? { ...prev, photos: prev.photos.filter((p) => p.id !== id) }
          : prev
      );
    } catch (err) {
      setPhotosErr(
        err instanceof PortalApiError ? err.message : "Could not delete photo"
      );
    } finally {
      setBusy(false);
    }
  }

  const displayAvatar =
    avatarPreview ||
    (profile?.avatar_thumb_url || profile?.avatar_url || "").trim();

  if (!token) {
    return (
      <div className="space-y-4">
        <p className="text-sm text-muted">Sign in to manage your profile.</p>
        <Link
          href="/portal"
          className="text-sm font-medium text-foreground underline underline-offset-2"
        >
          Back to portal sign-in
        </Link>
      </div>
    );
  }

  if (loadError) {
    return (
      <div className="space-y-4">
        <p className="text-sm text-muted">{loadError}</p>
        <Link
          href="/portal"
          className="text-sm font-medium text-foreground underline underline-offset-2"
        >
          Back to portal
        </Link>
      </div>
    );
  }

  if (!profile && busy) {
    return <p className="text-sm text-muted">Loading profile…</p>;
  }

  if (!profile) {
    return null;
  }

  return (
    <div className="space-y-10">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">
          Profile settings
        </h1>
        <p className="mt-2 text-sm text-muted">
          Update your account details, avatar, password, and gallery photos.
        </p>
      </div>

      <section className="space-y-4 rounded-lg border border-border/80 bg-muted-bg/10 p-6">
        <h2 className="text-lg font-medium">Avatar</h2>
        {avatarErr ? (
          <p className="text-sm text-red-600 dark:text-red-400">{avatarErr}</p>
        ) : null}
        <div className="flex flex-wrap items-start gap-6">
          <div className="relative h-28 w-28 overflow-hidden rounded-full border border-border bg-muted-bg">
            {displayAvatar ? (
              <img
                src={displayAvatar}
                alt=""
                className="h-full w-full object-cover"
                onError={(ev) => {
                  (ev.target as HTMLImageElement).style.display = "none";
                }}
              />
            ) : (
              <div className="flex h-full w-full items-center justify-center text-xs text-muted">
                No avatar
              </div>
            )}
          </div>
          <div className="flex flex-col gap-2">
            <input
              ref={avatarFileInputRef}
              type="file"
              accept="image/jpeg,image/png,image/webp"
              disabled={busy}
              className="max-w-xs text-sm file:mr-2 file:rounded-md file:border file:border-border file:bg-background file:px-2 file:py-1"
              onChange={(ev) => {
                const f = ev.target.files?.[0];
                if (f) {
                  onAvatarFileChange(f);
                }
                ev.target.value = "";
              }}
            />
            <div className="flex flex-wrap gap-2">
              <button
                type="button"
                disabled={busy || !avatarPreview}
                onClick={() => {
                  const f = avatarFileInputRef.current?.files?.[0];
                  if (f) {
                    void onUploadAvatar(f);
                  }
                }}
                className="rounded-md bg-foreground px-3 py-1.5 text-sm font-medium text-background disabled:opacity-50"
              >
                Upload selected
              </button>
              <button
                type="button"
                disabled={busy || !profile.avatar_url}
                onClick={() => void onRemoveAvatar()}
                className="rounded-md border border-border px-3 py-1.5 text-sm font-medium hover:bg-muted-bg disabled:opacity-50"
              >
                Remove avatar
              </button>
            </div>
          </div>
        </div>
      </section>

      <section className="space-y-4 rounded-lg border border-border/80 bg-muted-bg/10 p-6">
        <h2 className="text-lg font-medium">Profile</h2>
        {profileMessage ? (
          <p className="text-sm text-green-700 dark:text-green-400">
            {profileMessage}
          </p>
        ) : null}
        {profileErr ? (
          <p className="text-sm text-red-600 dark:text-red-400">{profileErr}</p>
        ) : null}
        <form className="max-w-lg space-y-4" onSubmit={(e) => void onSaveProfile(e)}>
          <div>
            <label className="block text-sm font-medium" htmlFor="name">
              Name
            </label>
            <input
              id="name"
              value={name}
              onChange={(ev) => setName(ev.target.value)}
              required
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium" htmlFor="email">
              Email
            </label>
            <input
              id="email"
              type="email"
              value={email}
              onChange={(ev) => setEmail(ev.target.value)}
              required
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium" htmlFor="phone">
              Phone
            </label>
            <input
              id="phone"
              value={phone}
              onChange={(ev) => setPhone(ev.target.value)}
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium" htmlFor="designation">
              Designation
            </label>
            <input
              id="designation"
              value={designation}
              onChange={(ev) => setDesignation(ev.target.value)}
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium" htmlFor="bio">
              Bio
            </label>
            <textarea
              id="bio"
              value={bio}
              onChange={(ev) => setBio(ev.target.value)}
              rows={4}
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <button
            type="submit"
            disabled={busy}
            className="rounded-md bg-foreground px-4 py-2 text-sm font-medium text-background disabled:opacity-50"
          >
            Save profile
          </button>
        </form>
      </section>

      <section className="space-y-4 rounded-lg border border-border/80 bg-muted-bg/10 p-6">
        <h2 className="text-lg font-medium">Password</h2>
        {passwordMessage ? (
          <p className="text-sm text-green-700 dark:text-green-400">
            {passwordMessage}
          </p>
        ) : null}
        {passwordErr ? (
          <p className="text-sm text-red-600 dark:text-red-400">
            {passwordErr}
          </p>
        ) : null}
        <form
          className="max-w-lg space-y-4"
          onSubmit={(e) => void onSavePassword(e)}
        >
          <div>
            <label className="block text-sm font-medium" htmlFor="cur-pw">
              Current password
            </label>
            <input
              id="cur-pw"
              type="password"
              autoComplete="current-password"
              value={currentPassword}
              onChange={(ev) => setCurrentPassword(ev.target.value)}
              required
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium" htmlFor="new-pw">
              New password
            </label>
            <input
              id="new-pw"
              type="password"
              autoComplete="new-password"
              value={newPassword}
              onChange={(ev) => setNewPassword(ev.target.value)}
              required
              minLength={8}
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <div>
            <label className="block text-sm font-medium" htmlFor="conf-pw">
              Confirm new password
            </label>
            <input
              id="conf-pw"
              type="password"
              autoComplete="new-password"
              value={confirmPassword}
              onChange={(ev) => setConfirmPassword(ev.target.value)}
              required
              disabled={busy}
              className="mt-1 w-full rounded-md border border-border bg-background px-3 py-2 text-sm"
            />
          </div>
          <button
            type="submit"
            disabled={busy}
            className="rounded-md bg-foreground px-4 py-2 text-sm font-medium text-background disabled:opacity-50"
          >
            Update password
          </button>
        </form>
      </section>

      <section className="space-y-4 rounded-lg border border-border/80 bg-muted-bg/10 p-6">
        <h2 className="text-lg font-medium">Photos</h2>
        {photosErr ? (
          <p className="text-sm text-red-600 dark:text-red-400">{photosErr}</p>
        ) : null}
        <input
          ref={photoFileInputRef}
          type="file"
          accept="image/jpeg,image/png,image/webp"
          disabled={busy}
          className="max-w-xs text-sm file:mr-2 file:rounded-md file:border file:border-border file:bg-background file:px-2 file:py-1"
          onChange={(ev) => {
            const f = ev.target.files?.[0];
            if (f) {
              void onUploadPhoto(f);
            }
            ev.target.value = "";
          }}
        />
        <ul className="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
          {profile.photos.map((p: PortalProfilePhoto) => (
            <li
              key={p.id}
              className="group relative overflow-hidden rounded-md border border-border"
            >
              <img
                src={p.thumb_url || p.url}
                alt={p.name}
                className="aspect-square w-full object-cover"
              />
              <button
                type="button"
                disabled={busy}
                onClick={() => void onDeletePhoto(p.id)}
                className="absolute right-2 top-2 rounded bg-background/90 px-2 py-0.5 text-xs font-medium opacity-0 transition-opacity group-hover:opacity-100 disabled:opacity-50"
              >
                Delete
              </button>
            </li>
          ))}
        </ul>
      </section>

      <Link
        href="/portal"
        className="inline-block text-sm text-muted underline-offset-2 hover:text-foreground hover:underline"
      >
        Back to portal
      </Link>
    </div>
  );
}
