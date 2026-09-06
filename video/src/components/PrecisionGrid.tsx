import React from 'react';
import { interpolate, useCurrentFrame, useVideoConfig } from 'remotion';

interface PrecisionGridProps {
  locationLabel?: string;
  coordinates?: string;
  statusLabel?: string;
  showScanline?: boolean;
}

export const PrecisionGrid: React.FC<PrecisionGridProps> = ({
  locationLabel = 'COSTA MEDITERRÁNEA · ESPAÑA',
  coordinates = '36.5101° N, 4.8824° W',
  statusLabel = 'INTEL SENSOR ACTIVE',
  showScanline = true,
}) => {
  const frame = useCurrentFrame();
  const { width, height } = useVideoConfig();

  // Slow subtle pulse
  const opacity = interpolate(
    Math.sin(frame / 18),
    [-1, 1],
    [0.15, 0.3]
  );

  // Scanline sweep
  const scanY = interpolate(
    frame % 120,
    [0, 120],
    [-50, height + 50]
  );

  return (
    <div
      style={{
        position: 'absolute',
        top: 0,
        left: 0,
        width,
        height,
        pointerEvents: 'none',
        zIndex: 5,
      }}
    >
      {/* Precision Corner Brackets */}
      {/* Top Left */}
      <div
        style={{
          position: 'absolute',
          top: 36,
          left: 48,
          width: 32,
          height: 32,
          borderTop: '2px solid rgba(191, 161, 95, 0.5)',
          borderLeft: '2px solid rgba(191, 161, 95, 0.5)',
        }}
      />
      {/* Top Right */}
      <div
        style={{
          position: 'absolute',
          top: 36,
          right: 48,
          width: 32,
          height: 32,
          borderTop: '2px solid rgba(191, 161, 95, 0.5)',
          borderRight: '2px solid rgba(191, 161, 95, 0.5)',
        }}
      />
      {/* Bottom Left */}
      <div
        style={{
          position: 'absolute',
          bottom: 36,
          left: 48,
          width: 32,
          height: 32,
          borderBottom: '2px solid rgba(191, 161, 95, 0.5)',
          borderLeft: '2px solid rgba(191, 161, 95, 0.5)',
        }}
      />
      {/* Bottom Right */}
      <div
        style={{
          position: 'absolute',
          bottom: 36,
          right: 48,
          width: 32,
          height: 32,
          borderBottom: '2px solid rgba(191, 161, 95, 0.5)',
          borderRight: '2px solid rgba(191, 161, 95, 0.5)',
        }}
      />

      {/* Top HUD Bar */}
      <div
        style={{
          position: 'absolute',
          top: 42,
          left: 96,
          right: 96,
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          fontSize: 13,
          letterSpacing: '0.22em',
          fontWeight: 600,
          color: 'rgba(191, 161, 95, 0.85)',
          textTransform: 'uppercase',
        }}
      >
        <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
          <div
            style={{
              width: 7,
              height: 7,
              borderRadius: '50%',
              backgroundColor: '#BFA15F',
              boxShadow: '0 0 10px #BFA15F',
            }}
          />
          <span>ROMVILL INTELLIGENCE GRID</span>
          <span style={{ color: 'rgba(255,255,255,0.3)', margin: '0 4px' }}>|</span>
          <span style={{ color: 'rgba(242, 245, 250, 0.65)' }}>{locationLabel}</span>
        </div>

        <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
          <span style={{ color: 'rgba(242, 245, 250, 0.5)', fontFamily: 'monospace' }}>
            {coordinates}
          </span>
          <div
            style={{
              padding: '3px 9px',
              borderRadius: 3,
              backgroundColor: 'rgba(19, 91, 236, 0.2)',
              border: '1px solid rgba(19, 91, 236, 0.45)',
              color: '#7FA8F7',
              fontSize: 11,
              letterSpacing: '0.18em',
            }}
          >
            {statusLabel}
          </div>
        </div>
      </div>

      {/* Bottom Telemetry Bar */}
      <div
        style={{
          position: 'absolute',
          bottom: 42,
          left: 96,
          right: 96,
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          fontSize: 12,
          letterSpacing: '0.2em',
          fontWeight: 600,
          color: 'rgba(169, 180, 198, 0.6)',
          textTransform: 'uppercase',
        }}
      >
        <div>SYSTEM: DUE DILIGENCE · VERIFIED OSINT + FIELDWORK</div>
        <div style={{ display: 'flex', gap: 20 }}>
          <span>SEC: CLASS A</span>
          <span>SOURCES: 40+ GOV</span>
          <span style={{ color: '#BFA15F' }}>INDEPENDENT AUDIT</span>
        </div>
      </div>

      {/* Subtle Scanline */}
      {showScanline && (
        <div
          style={{
            position: 'absolute',
            top: scanY,
            left: 0,
            width: '100%',
            height: '2px',
            background:
              'linear-gradient(90deg, transparent, rgba(191, 161, 95, 0.35), rgba(19, 91, 236, 0.4), transparent)',
            boxShadow: '0 0 16px rgba(191, 161, 95, 0.3)',
          }}
        />
      )}

      {/* Subtle Hairline Background Grid */}
      <div
        style={{
          position: 'absolute',
          inset: 0,
          opacity,
          backgroundImage:
            'linear-gradient(rgba(191, 161, 95, 0.12) 1px, transparent 1px), linear-gradient(90deg, rgba(191, 161, 95, 0.12) 1px, transparent 1px)',
          backgroundSize: '120px 120px',
        }}
      />
    </div>
  );
};
